<?php

namespace App\Livewire\Transactions;

use App\Models\Product;
use Livewire\Component;
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

    #[Validate('required_if:type,adjustment|string|max:500')]
    public $notes = '';

    #[Validate('required_if:type,adjustment|in:damaged,lost,donation,stock_take,other')]
    public $adjustment_reason = null;

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
                    break;

                case TransactionType::Return:
                    $product->increment('quantity_in_stock', $this->quantity);
                    break;

                case TransactionType::Adjustment:
                    // Validate final stock won't go negative
                    if (($product->quantity_in_stock + $this->quantity) < 0) {
                        throw new \Exception('Adjustment would result in negative stock.');
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

            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create transaction: ' . $e->getMessage()
            );
        }
    }

}
