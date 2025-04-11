<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use App\Models\Transaction;
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

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    #[On('edit-product')]
    public function editProduct($productId)
    {
        $this->resetValidation();
        $this->product = Product::where('id', $productId)->first();
        $this->fillInputs($this->product);
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

    public function updateProduct()
    {
        $validated = $this->validate();

        // Check if a product with the same SKU already exists, excluding the current product
        $existingProduct = Product::where('sku', $this->sku)
            ->where('id', '!=', $this->product->id) // Exclude the current product
            ->first();

        if ($existingProduct) {
            $this->addError('sku', 'The SKU must be unique.');
            return;
        }

        try {
            DB::beginTransaction();

            // Check if the stock has been updated
            if ($this->product->quantity_in_stock != $this->quantity_in_stock) {
                // If the new stock quantity is greater than the old stock quantity, add a purchase transaction
                if ($this->quantity_in_stock > $this->product->quantity_in_stock) {
                    // Record the purchase transaction (restocking)
                    Transaction::create([
                        'product_id' => $this->product->id,
                        'type' => 'purchase', // Type is 'purchase' for restocking
                        'quantity' => $this->quantity_in_stock - $this->product->quantity_in_stock, // Calculate quantity change
                        'created_by' => Auth::id(), // Use the currently authenticated user
                        'notes' => 'Restocking from supplier', // You can add more details if needed
                    ]);

                    // Clear transaction cache if quantity increased
                    Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
                }

                // Update last restocked timestamp if quantity has changed
                $validated['last_restocked'] = now();
            }

            $this->authorize('edit', $this->product);
            $this->product->update($validated);
            DB::commit();

            // Check if supplier has changed and clear supplier cache if necessary
            if ($this->product->supplier_id !== $validated['supplier_id']) {
                Cache::forget('suppliers:page:1:per_page:10:sort:created_at:dir:DESC:search::product:');
            }

            $this->reset();

            // Dispatch events after successful update
            $this->dispatch('modal-close', name: 'edit-product');
            $this->dispatch('product-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Product updated successfully!'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error and dispatch failure notification
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
            'suppliers' => Supplier::orderBy('name')->get(),
            'categories' => Product::distinct()->orderBy('category')->pluck('category'),
        ]);
    }
}
