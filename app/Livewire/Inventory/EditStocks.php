<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditStocks extends Component
{
    public ?Product $product = null;

    #[Validate('required|integer|min:0')]
    public int $quantity_in_stock = 0;

    #[Validate('required|integer|min:0')]
    public int $reorder_threshold = 10;

    #[Validate('required|integer|min:0')]
    public int $safety_stock = 5;

    #[On('edit-stocks')]
    public function loadProduct($productId)
    {
        $this->resetValidation();
        $this->product = Product::find($productId);
        $this->quantity_in_stock = $this->product->quantity_in_stock;
        $this->reorder_threshold = $this->product->reorder_threshold;
        $this->safety_stock = $this->product->safety_stock;
    }

    public function updateStock()
    {
        $validated = $this->validate();

        try {
            DB::beginTransaction();

            // Check if the new stock is greater than the current stock
            if ($this->quantity_in_stock > $this->product->quantity_in_stock) {
                // Record the restock transaction (purchase) only when stock is increased
                Transaction::create([
                    'product_id' => $this->product->id,
                    'type' => 'purchase', // Type is 'purchase' for restocking
                    'quantity' => $this->quantity_in_stock - $this->product->quantity_in_stock, // Calculate quantity change
                    'created_by' => Auth::id(), // Use the currently authenticated user
                    'notes' => 'Restocking from supplier', // You can add more details if needed
                ]);
            }

            $this->product->update([
                ...$validated,
                'last_restocked' => now()
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'edit-stocks');
            $this->dispatch('product-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Stock updated successfully!'
            );
            Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock update failed: ' . $e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update stock.'
            );
        }
    }
}
