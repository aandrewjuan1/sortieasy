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

    public function save()
    {
        $this->validate();

        if ($this->quantity > $this->available_stock) {
            $this->quantityError = "Quantity exceeds available stock of {$this->available_stock}";
            return;
        }

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);

            Logistic::create([
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'delivery_date' => $this->delivery_date,
                'status' => $this->status
            ]);

            // ✅ Update stock if status is shipped or delivered
            if (in_array($this->status, ['shipped', 'delivered'])) {
                $product->decrement('quantity_in_stock', $this->quantity);
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
                message: 'Failed to create logistics entry.'
            );
        }
    }


    public function updatedProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->available_stock = $product->quantity_in_stock;
                return;
            }
        }
        $this->available_stock = 0;
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }
}
