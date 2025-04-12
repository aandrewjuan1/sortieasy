<?php

namespace App\Livewire\Logistics;

use App\Models\Product;
use Livewire\Component;
use App\Models\Logistic;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddLogistic extends Component
{
    #[Validate('required|exists:products,id')]
    public int $product_id = 0;

    #[Validate('required|integer|min:1')]
    public int $quantity = 1;

    #[Validate('required|date|after_or_equal:today')]
    public string $delivery_date = '';

    #[Validate('required|in:pending,shipped,delivered')]
    public string $status = 'pending';

    public function mount()
    {
        $this->delivery_date = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);

            // Check stock if status is shipped
            if ($this->status === 'shipped' && $product->quantity_in_stock < $this->quantity) {
                throw new \Exception('Insufficient stock available');
            }

            $logistic = Logistic::create([
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'delivery_date' => $this->delivery_date,
                'status' => $this->status
            ]);

            // Handle stock changes based on status
            if ($this->status === 'shipped') {
                $product->decrement('quantity_in_stock', $this->quantity);

                // Check if stock falls below reorder threshold
                if ($product->quantity_in_stock <= $product->reorder_threshold) {
                    $this->dispatch('notify',
                        type: 'warning',
                        message: "Product {$product->name} is now below reorder threshold!"
                    );
                }
            }
            // If directly marked as "delivered" (unlikely, but possible)
            elseif ($this->status === 'delivered') {
                // Ensure stock was already deducted when shipped
                if ($logistic->status !== 'shipped') {
                    $product->decrement('quantity_in_stock', $this->quantity);
                }

                // Record delivery completion (e.g., update last_delivered_at)
                $product->update(['last_restocked' => now()]);

                $this->dispatch('notify',
                    type: 'info',
                    message: "Product {$product->name} successfully delivered!"
                );
            }

            DB::commit();

            $this->reset();
            $this->delivery_date = now()->format('Y-m-d');

            $this->dispatch('modal-close', name: 'add-logistic');
            $this->dispatch('logistic-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Logistics entry created successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create logistics entry: ' . $e->getMessage()
            );
        }
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }
}
