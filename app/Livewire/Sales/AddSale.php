<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use Livewire\Component;
use App\Enums\SaleChannel;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AddSale extends Component
{
    #[Validate('required|exists:products,id')]
    public $product_id = null;

    #[Validate('required|integer|min:1')]
    public $quantity = 1;

    #[Validate('required|numeric|min:0.01|decimal:0,2')]
    public $total_price = 0.00;

    #[Validate('required|in:in_store,online,phone')]
    public $channel = SaleChannel::InStore->value;

    #[Validate('required|date')]
    public $sale_date;

    public $available_stock = 0;
    public $unit_price = 0.00;

    public function updatedProductId($value)
    {
        $this->resetValidation();
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->available_stock = $product->quantity_in_stock;
                $this->unit_price = $product->price;
                $this->calculateTotal();

                return; // 👈 Exit early to avoid resetting values
            }
        }
        // Only reset if no product is selected
        $this->quantityError = null;
        $this->available_stock = 0;
        $this->unit_price = 0.00;
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_price = round(
            (float)$this->quantity * (float)$this->unit_price,
            2
        );
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['quantity', 'unit_price', 'product_id'])) {
            $this->calculateTotal();
        }

        if ($propertyName === 'quantity') {
            $this->validate([
                'quantity' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                    if ($this->product_id && $value > $this->available_stock) {
                        $fail("Quantity exceeds available stock of {$this->available_stock}");
                    }
                }]
            ]);
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::where('id', $this->product_id)
                ->lockForUpdate()
                ->first();

            if ($product->quantity_in_stock < $this->quantity) {
                throw new \Exception("Insufficient stock. Available: {$product->quantity_in_stock}");
            }

            $sale = Sale::create([
                'product_id' => $this->product_id,
                'user_id' => Auth::id(), // Set current user as the sale creator
                'quantity' => $this->quantity,
                'unit_price' => $this->unit_price,
                'total_price' => $this->total_price,
                'channel' => $this->channel,
                'sale_date' => $this->sale_date,
            ]);

            $product->decrement('quantity_in_stock', $this->quantity);

            Transaction::create([
                'product_id' => $this->product_id,
                'type' => TransactionType::Sale->value,
                'quantity' => $this->quantity,
                'notes' => "Sale #{$sale->id} via {$this->channel} channel",
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            $this->resetExcept(['channel']);
            $this->sale_date = now()->format('Y-m-d');
            $this->calculateTotal();

            $this->dispatch('modal-close', name: 'add-sale');
            $this->dispatch('sale-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Sale recorded successfully! Stock updated.'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
            Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to record sale.'
            );
        }
    }

    #[Computed]
    public function products()
    {
        return Product::where('quantity_in_stock', '>', 0)
                      ->orderBy('name')
                      ->get();
    }
}
