<?php

namespace App\Livewire\Logistics;

use App\Models\Product;
use Livewire\Component;
use App\Models\Logistic;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditLogistic extends Component
{
    #[Validate('required|exists:products,id')]
    public int $product_id = 0;

    #[Validate('required|integer|min:1')]
    public int $quantity = 1;

    #[Validate('required|date|after_or_equal:today')]
    public string $delivery_date = '';

    #[Validate('required|in:pending,shipped,delivered')]
    public string $status = 'pending';
    public ?Logistic $logistic = null;
    public ?Product $product = null;

    public $available_stock = 0;

    public $quantityError = null;

    public function updatedQuantity($value)
    {
        if ($value > $this->available_stock) {
            $this->quantityError = "Quantity exceeds available stock of {$this->available_stock}";
            return;
        } else {
            $this->quantityError = null;
        }
    }

    public function fillInputs($logistic)
    {
        $this->product_id = $logistic->product_id;
        $this->quantity = $logistic->quantity;
        $this->delivery_date = $logistic->delivery_date->format('Y-m-d');
        $this->status = $logistic->status->value;
        $this->product = Product::find($logistic->product_id);
    }

    #[On('edit-logistic')]
    public function editLogistic($logisticId)
    {
        $this->logistic = Logistic::where('id', $logisticId)->first();
        $this->fillInputs($this->logistic);
        $this->quantityError = null;
        $this->resetValidation();
    }

    public function update()
    {
        $this->validate();

        if ($this->quantity > $this->available_stock) {
            $this->quantityError = "Quantity exceeds available stock of {$this->available_stock}";
            return;
        }

        DB::beginTransaction();

        try {
            $originalProduct = Product::findOrFail($this->logistic->product_id);
            $newProduct = Product::findOrFail($this->product_id);

            $originalStatus = $this->logistic->status->value;
            $originalQuantity = $this->logistic->quantity;
            $newStatus = $this->status;

            // Product changed
            $productChanged = $this->product_id !== $this->logistic->product_id;

            // Handle stock rollback for old product if needed
            if ($originalStatus === 'shipped') {
                $originalProduct->increment('quantity_in_stock', $originalQuantity);
            }

            // Handle new product stock deduction if needed
            if ($newStatus === 'shipped') {
                if ($newProduct->quantity_in_stock < $this->quantity) {
                    throw new \Exception('Insufficient stock available for the selected product');
                }
                $newProduct->decrement('quantity_in_stock', $this->quantity);
            }

            // Update the logistic entry
            $this->logistic->update([
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'delivery_date' => $this->delivery_date,
                'status' => $newStatus,
            ]);

            // Check stock level after update
            if ($newStatus === 'shipped' && $newProduct->quantity_in_stock <= $newProduct->reorder_threshold) {
                $this->dispatch('notify',
                    type: 'warning',
                    message: "Product {$newProduct->name} is now below the reorder threshold!"
                );
            }

            DB::commit();

            $this->dispatch('modal-close', name: 'edit-logistic');
            $this->dispatch('logistic-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Logistics entry updated successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update logistics entry.'
            );
        }
    }


    public function delete()
    {
        $this->authorize('delete', Auth::user());
        $this->logistic->delete();

        $this->dispatch('modal-close', name: 'delete-logistic');
        $this->dispatch('modal-close', name: 'edit-logistic');
        $this->dispatch('logistic-deleted');
        $this->dispatch('notify',
            type: 'success',
            message: 'Logistic deleted successfully!'
        );

        Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
        Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
    }
}
