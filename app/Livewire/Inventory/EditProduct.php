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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditProduct extends Component
{
    public ?Product $product = null;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|max:255')]
    public string $category = '';

    #[Validate('required|max:255')]
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

    protected int $originalSupplierId = 0;

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    #[On('edit-product')]
    public function editProduct($productId)
    {
        $this->resetValidation();
        $this->product = Product::findOrFail($productId);
        $this->fillInputs($this->product);
        $this->originalSupplierId = $this->product->supplier_id;
    }

    public function generateSKU()
    {
        if (empty($this->sku)) {
            $categoryPrefix = Str::upper(Str::substr($this->category, 0, 3));
            $randomString = Str::upper(Str::random(4));
            $this->sku = $categoryPrefix . '-' . $randomString;
        }
    }

    public function fillInputs($product)
    {
        $this->name = $product->name;
        $this->category = $product->category;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->cost = $product->cost;
        $this->quantity_in_stock = $product->quantity_in_stock;
        $this->reorder_threshold = $product->reorder_threshold;
        $this->safety_stock = $product->safety_stock;
        $this->supplier_id = $product->supplier_id;
    }

    public function update()
    {
        $validated = $this->validate();

        // Ensure SKU is unique
        $existingProduct = Product::where('sku', $this->sku)
            ->where('id', '!=', $this->product->id)
            ->first();

        if ($existingProduct) {
            $this->addError('sku', 'The SKU must be unique.');
            return;
        }

        $this->authorize('edit', $this->product);

        DB::beginTransaction();

        try {
            // Handle stock changes
            if ($this->quantity_in_stock > $this->product->quantity_in_stock) {
                $difference = $this->quantity_in_stock - $this->product->quantity_in_stock;

                Transaction::create([
                    'product_id' => $this->product->id,
                    'type' => 'purchase',
                    'quantity' => $difference,
                    'created_by' => Auth::id(),
                    'notes' => 'Restocking from supplier',
                ]);

                $validated['last_restocked'] = now();

                Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
            }

            $this->product->update($validated);
            DB::commit();

            // Check if supplier has changed
            if ($this->supplier_id !== $this->originalSupplierId) {
                Cache::forget('suppliers:page:1:per_page:10:sort:created_at:dir:DESC:search::product:');
            }

            $this->reset();

            $this->dispatch('modal-close', name: 'edit-product');
            $this->dispatch('product-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Product updated successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update product.'
            );
        }
    }

    public function render()
    {
        return view('livewire.inventory.edit-product', [
            'suppliers' => $this->suppliers(),
            'categories' => Product::distinct()->orderBy('category')->pluck('category'),
        ]);
    }
}
