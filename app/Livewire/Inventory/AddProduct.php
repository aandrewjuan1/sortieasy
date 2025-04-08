<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddProduct extends Component
{
    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|max:255')]
    public string $category = '';

    #[Validate('required|max:100|unique:products,sku')]
    public string $sku = '';

    #[Validate('nullable|max:1000')]
    public ?string $description = null;

    #[Validate('required|numeric|min:0.01|max:999999.99|decimal:0,2')]
    public string $price = '';

    #[Validate('nullable|numeric|min:0|max:999999.99|decimal:0,2')]
    public ?string $cost = null;

    #[Validate('required|integer|min:0')]
    public int $quantity_in_stock = 0;

    #[Validate('required|integer|min:0')]
    public int $reorder_threshold = 10;

    #[Validate('required|integer|min:0')]
    public int $safety_stock = 5;

    #[Validate('required|exists:suppliers,id')]
    public ?int $supplier_id = null;

    public function generateSKU()
    {
        if (empty($this->sku)) {
            $categoryPrefix = Str::upper(Str::substr($this->category, 0, 3));
            $randomString = Str::upper(Str::random(4));
            $this->sku = $categoryPrefix . '-' . $randomString;
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $this->name,
                'category' => $this->category,
                'sku' => $this->sku,
                'description' => $this->description,
                'price' => $this->price,
                'cost' => $this->cost,
                'quantity_in_stock' => $this->quantity_in_stock,
                'reorder_threshold' => $this->reorder_threshold,
                'safety_stock' => $this->safety_stock,
                'supplier_id' => $this->supplier_id,
                'last_restocked' => $this->quantity_in_stock > 0 ? now() : null,
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'add-product');
            $this->dispatch('product-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Product added successfully!'
            );
            Cache::forget('suppliers:page:1:per_page:10:sort:created_at:dir:DESC:search::product:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('error', message: 'Failed to create product: ' . $e->getMessage());

            // You could also add more specific error handling here
            // For example, for unique constraint violations:
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError('sku', 'This SKU already exists');
            }
        }
    }

    public function render()
    {
        return view('livewire.inventory.add-product', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'categories' => Product::distinct()->orderBy('category')->pluck('category'),
        ]);
    }
}
