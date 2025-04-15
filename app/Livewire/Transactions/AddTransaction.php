<?php

namespace App\Livewire\Transactions;

use App\Models\Sale;
use App\Models\Product;
use Livewire\Component;
use App\Enums\SaleChannel;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AddTransaction extends Component
{
    #[Validate('required|exists:products,id')]
    public $product_id;

    #[Validate('required|in:purchase,sale,return,adjustment')]
    public $type;

    #[Validate('required|integer|min:1')]
    public $quantity;

    #[Validate('nullable|string|max:500')]
    public $notes = '';

    public $adjustment_reason = null;

    public $available_stock = 0;

    public $adjustmentReasons = [
        'damaged' => 'Damaged Goods',
        'lost' => 'Lost/Missing',
        'donation' => 'Donation/Gift',
        'stock_take' => 'Stock Take Correction',
        'other' => 'Other Reason'
    ];

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }

    public function resetVal()
    {
        $this->resetValidation();
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



    protected function rules()
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:purchase,sale,return,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ];

        if ($this->type === 'adjustment') {
            $rules['adjustment_reason'] = 'required|in:damaged,lost,donation,stock_take,other';
            $rules['notes'] = 'required|string|max:500';
        }

        return $rules;
    }

    public function updated($property)
    {
        if ($property === 'type') {
            $this->reset('adjustment_reason');
            $this->resetValidation();
        }

        if ($property === 'quantity' || $property === 'type') {
            if ($this->type === 'sale' && $this->product_id && $this->quantity) {
                $this->validate([
                    'quantity' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                        if ($this->available_stock > 0 && $value > $this->available_stock) {
                            $fail("Quantity exceeds available stock of {$this->available_stock}");
                        }
                    }]
                ]);
            }
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);
            $type = TransactionType::from($this->type);
            $previousStock = $product->quantity_in_stock;

            // Build notes with reason if adjustment
            $notes = $this->notes;
            if ($type === TransactionType::Adjustment) {
                $reason = $this->adjustmentReasons[$this->adjustment_reason] ?? $this->adjustment_reason;
                $notes = "Reason: {$reason}. Previous stock: {$previousStock}. " . $this->notes;
            }

            $transaction = Transaction::create([
                'product_id' => $this->product_id,
                'type' => $type,
                'quantity' => $this->quantity,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);

            // Handle stock changes
            switch ($type) {
                case TransactionType::Purchase:
                    $product->increment('quantity_in_stock', $this->quantity);
                    $product->last_restocked = now();
                    break;

                case TransactionType::Sale:
                    if ($product->quantity_in_stock < $this->quantity) {
                        throw new \Exception('Not enough stock available for this sale.');
                    }
                    $product->decrement('quantity_in_stock', $this->quantity);

                    // Create sale record using product's price field
                    Sale::create([
                        'product_id' => $this->product_id,
                        'user_id' => Auth::id(),
                        'quantity' => $this->quantity,
                        'unit_price' => $product->price, // Using price instead of selling_price
                        'total_price' => $product->price * $this->quantity,
                        'channel' => $this->channel ?? SaleChannel::InStore->value, // Default to in-store
                        'sale_date' => now()->toDateString(),
                    ]);
                    break;

                case TransactionType::Return:
                    $product->increment('quantity_in_stock', $this->quantity);
                    break;

                case TransactionType::Adjustment:
                    // Validate final stock won't go negative
                    if ($this->quantity < 0) {
                        throw new \Exception('Adjusted stock cannot be negative.');
                    }

                    $product->quantity_in_stock = $this->quantity;

                    // Check safety stock
                    if ($product->quantity_in_stock < $product->safety_stock) {
                        $this->dispatch('notify',
                            type: 'warning',
                            message: 'Stock adjusted below safety level!'
                        );
                    }
                    break;
            }

            $product->save();
            DB::commit();

            $this->reset();
            $this->dispatch('modal-close', name: 'add-transaction');
            $this->dispatch('transaction-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Transaction created successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
            Cache::forget('sales:page:1:per_page:10:sort:created_at:dir:DESC:search::channel::date:');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create transaction.'
            );
        }
    }
}
