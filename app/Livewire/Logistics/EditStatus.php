<?php

namespace App\Livewire\Logistics;

use Livewire\Component;
use App\Models\Logistic;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class EditStatus extends Component
{
    public ?Logistic $logistic = null;
    public string $status = 'pending';

    // Display-only properties (not bound to inputs)
    public ?string $product_name = null;
    public ?int $quantity = null;
    public ?string $delivery_date = null;
    public ?int $available_stock = null;

    #[On('edit-status')]
    public function editStatus($logisticId)
    {
        $this->logistic = Logistic::findOrFail($logisticId);
        $this->status = $this->logistic->status->value;
        $this->product_name = $this->logistic->product->name;
        $this->quantity = $this->logistic->quantity;
        $this->delivery_date = $this->logistic->delivery_date->format('Y-m-d');
        $this->available_stock = $this->logistic->product->quantity_in_stock;
    }

    public function updateStatus()
    {
        // Validate only the status field
        $this->validate([
            'status' => 'required|in:pending,shipped,delivered'
        ]);

        DB::beginTransaction();

        try {
            $originalStatus = $this->logistic->status->value;
            $newStatus = $this->status;

            // Handle stock changes
            if ($originalStatus === 'shipped' && $newStatus !== 'shipped') {
                $this->logistic->product->increment('quantity_in_stock', $this->logistic->quantity);
            } elseif ($originalStatus !== 'shipped' && $newStatus === 'shipped') {
                if ($this->logistic->product->quantity_in_stock < $this->logistic->quantity) {
                    $this->dispatch('notify',
                        type: 'error',
                        message: 'Insufficient stock available!'
                    );
                    return;
                }
                $this->logistic->product->decrement('quantity_in_stock', $this->logistic->quantity);
            }

            $this->logistic->update(['status' => $newStatus]);

            DB::commit();

            $this->dispatch('modal-close', name: 'edit-status');
            $this->dispatch('logistic-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Status updated successfully!'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update status: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.logistics.edit-status');
    }
}
