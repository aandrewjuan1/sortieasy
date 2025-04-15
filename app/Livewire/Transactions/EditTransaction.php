<?php

namespace App\Livewire\Transactions;

use App\Models\Product;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Enums\TransactionType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EditTransaction extends Component
{
    #[Validate('required|exists:products,id')]
    public $product_id;

    #[Validate('required|in:purchase,sale,return,adjustment')]
    public $type;

    #[Validate('required|integer|min:1')]
    public $quantity;

    #[Validate('nullable|string|max:500')]
    public $notes = '';

    public ?Transaction $transaction = null;

    public $adjustment_reason = null;
    public ?Product $product = null;
    public $quantityError = null;

    public $adjustmentReasons = [
        'damaged' => 'Damaged Goods',
        'lost' => 'Lost/Missing',
        'donation' => 'Donation/Gift',
        'stock_take' => 'Stock Take Correction',
        'other' => 'Other Reason'
    ];

    public function resetVal()
    {
        $this->resetValidation();
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

    public function fillInputs($transaction)
    {
        $this->product_id = $transaction->product->id;
        $this->type = $transaction->type->value;
        $this->quantity = $transaction->quantity;
        $this->notes = $transaction->notes;
        $this->product = Product::find($transaction->product_id);

        // Extract adjustment reason from notes if exists
        if ($transaction->type === TransactionType::Adjustment) {
            foreach ($this->adjustmentReasons as $key => $label) {
                if (str_contains($transaction->notes, "Reason: {$label}")) {
                    $this->adjustment_reason = $key;
                    break;
                }
            }
        }
    }

    #[On('edit-transaction')]
    public function editTransaction($transactionId)
    {
        $this->transaction = Transaction::find($transactionId);
        $this->fillInputs($this->transaction);
        $this->resetValidation();
    }

    public function updated($property)
    {
        if (in_array($property, ['quantity', 'type', 'product_id'])) {
            $this->quantityError = null;

            if ($this->type === 'sale' && $this->product_id && $this->quantity) {
                if ($this->quantity > $this->available_stock) {
                    $this->quantityError = "Quantity exceeds available stock of {$this->available_stock}";
                }
            }
        }

        if ($property === 'type') {
            $this->reset('adjustment_reason');
            $this->resetValidation();
        }
    }

    public function update()
    {
       $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);

            $oldType = $this->transaction->type;
            $oldQty = $this->transaction->quantity;
            $newType = TransactionType::from($this->type);
            $currentStock = $product->quantity_in_stock;

            // Revert old transaction
            switch ($oldType) {
                case TransactionType::Purchase:
                case TransactionType::Return:
                    $currentStock -= $oldQty;
                    break;
                case TransactionType::Sale:
                    $currentStock += $oldQty;
                    break;
                case TransactionType::Adjustment:
                    // No revert needed as we'll set absolute value
                    break;
            }

            // Apply new transaction
            switch ($newType) {
                case TransactionType::Purchase:
                case TransactionType::Return:
                    $currentStock += $this->quantity;
                    break;
                case TransactionType::Sale:
                    $currentStock -= $this->quantity;
                    if ($currentStock < 0) {
                        throw new \Exception('Not enough stock available for this sale.');
                    }
                    break;
                case TransactionType::Adjustment:
                    // Validate final stock won't go negative
                    if ($this->quantity < 0) {
                        throw new \Exception('Adjusted stock cannot be negative.');
                    }

                    $currentStock = $this->quantity;

                    // Check safety stock
                    if ($currentStock < $product->safety_stock) {
                        $this->dispatch('notify',
                            type: 'warning',
                            message: 'Stock adjusted below safety level!'
                        );
                    }
                    break;
            }

            $product->update([
                'quantity_in_stock' => $currentStock,
            ]);

            // Build notes with reason if adjustment
            $notes = $this->notes;
            if ($newType === TransactionType::Adjustment) {
                $reason = $this->adjustmentReasons[$this->adjustment_reason] ?? $this->adjustment_reason;
                $notes = "Reason: {$reason}. Previous stock: {$product->quantity_in_stock}. " . $this->notes;
            }

            $this->transaction->update([
                'product_id' => $this->product_id,
                'type' => $newType,
                'quantity' => $this->quantity,
                'notes' => $notes,
            ]);

            DB::commit();

            $this->reset();
            $this->dispatch('modal-close', name: 'edit-transaction');
            $this->dispatch('transaction-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Transaction updated successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update transaction.'
            );
        }
    }

    public function delete()
    {
        DB::beginTransaction();

        try {
            $product = $this->transaction->product;
            $type = $this->transaction->type;
            $quantity = $this->transaction->quantity;

            // Revert the transaction's effect on stock
            switch ($type) {
                case TransactionType::Purchase:
                case TransactionType::Return:
                    $product->decrement('quantity_in_stock', $quantity);
                    break;
                case TransactionType::Sale:
                    $product->increment('quantity_in_stock', $quantity);
                    break;
                case TransactionType::Adjustment:
                    // Can't reliably revert adjustments - just delete the record
                    break;
            }

            $this->transaction->delete();
            DB::commit();

            $this->dispatch('modal-close', name: 'delete-transaction');
            $this->dispatch('modal-close', name: 'edit-transaction');
            $this->dispatch('transaction-deleted');
            $this->dispatch('notify',
                type: 'success',
                message: 'Transaction deleted successfully!'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to delete transaction.'
            );
        }
    }
}
