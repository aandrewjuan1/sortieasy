<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditStocks extends Component
{
    public ?Product $product = null;

    #[Validate('required|integer|min:0')]
    public int $quantity_in_stock = 0;

    #[Validate('required|integer|min:0')]
    public int $reorder_threshold = 10;

    #[Validate('required|integer|min:0')]
    public int $safety_stock = 5;

    #[On('add-stocks')]
    public function loadProduct($productId)
    {
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

            $this->product->update([
                ...$validated,
                'last_restocked' => now()
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'add-stocks');
            $this->dispatch('product-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Stock updated successfully!'
            );
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
