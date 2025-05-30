<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            $this->authorize('create', Product::class);

            // Create the new product
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

            // Add a purchase transaction if the product has stock
            if ($product->quantity_in_stock > 0) {
                Transaction::create([
                    'product_id' => $product->id,
                    'type' => 'purchase', // Type is 'purchase' for adding stock
                    'quantity' => $product->quantity_in_stock, // The stock quantity added
                    'created_by' => Auth::id(), // The user creating the product
                    'notes' => 'Product added to inventory', // You can add more details
                ]);
            }

            DB::commit();

            $this->reset();

            // Dispatch events after successful addition
            $this->dispatch('modal-close', name: 'add-product');
            $this->dispatch('product-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Product added successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
            Cache::forget('suppliers:page:1:per_page:10:sort:created_at:dir:DESC:search::product:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('error', message: 'Failed to create product');
        }
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return Product::distinct()->orderBy('category')->pluck('category');
    }
}
