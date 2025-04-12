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
        $this->resetValidation();
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);
            $originalStatus = $this->logistic->status->value; // Get the string value of the enum
            $originalQuantity = $this->logistic->quantity;
            $newStatus = $this->status; // This is already a string from the form input

            // Case 1: Changing from "shipped" to "pending" → Return stock
            if ($originalStatus === 'shipped' && $newStatus === 'pending') {
                $product->increment('quantity_in_stock', $originalQuantity);
            }
            // Case 2: Changing from "pending" to "shipped" → Deduct stock
            elseif ($originalStatus === 'pending' && $newStatus === 'shipped') {
                if ($product->quantity_in_stock < $this->quantity) {
                    throw new \Exception('Insufficient stock available');
                }
                $product->decrement('quantity_in_stock', $this->quantity);
            }
            // Case 3: Changing from "shipped" to "delivered" → No stock change
            elseif ($originalStatus === 'shipped' && $newStatus === 'delivered') {
                $product->update(['last_restocked' => now()]);
            }
            // Case 4: Changing quantity while in "shipped"
            elseif ($originalStatus === 'shipped' && $newStatus === 'shipped' && $this->quantity != $originalQuantity) {
                $difference = $originalQuantity - $this->quantity;
                if ($difference > 0) {
                    $product->increment('quantity_in_stock', $difference);
                } else {
                    $needed = abs($difference);
                    if ($product->quantity_in_stock < $needed) {
                        throw new \Exception('Insufficient stock for quantity adjustment');
                    }
                    $product->decrement('quantity_in_stock', $needed);
                }
            }

            // Update the logistic record
            $this->logistic->update([
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'delivery_date' => $this->delivery_date,
                'status' => $newStatus // Use the validated string value
            ]);

            // Check stock levels
            if (($newStatus === 'shipped' || $originalStatus === 'shipped') &&
                $product->quantity_in_stock <= $product->reorder_threshold) {
                $this->dispatch('notify',
                    type: 'warning',
                    message: "Product {$product->name} is now below reorder threshold!"
                );
            }

            DB::commit();

            $this->dispatch('modal-close', name: 'edit-logistic');
            $this->dispatch('logistic-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Logistics entry updated successfully!'
            );

            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update logistics entry: ' . $e->getMessage()
            );
        }
    }

    public function delete()
    {
        $this->logistic->delete();

        $this->dispatch('modal-close', name: 'delete-logistic');
        $this->dispatch('modal-close', name: 'edit-logistic');
        $this->dispatch('logistic-deleted');
        $this->dispatch('notify',
            type: 'success',
            message: 'Logistic deleted successfully!'
        );

        Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
    }
}
