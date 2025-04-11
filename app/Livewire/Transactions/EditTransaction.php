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

    public function fillInputs($transaction)
    {
        $this->product_id = $transaction->product->id;
        $this->type = $transaction->type->value;
        $this->quantity = $transaction->quantity;
        $this->notes = $transaction->notes;
    }

    #[On('edit-transaction')]
    public function editSupplier($transactionId)
    {
        $this->transaction = Transaction::find($transactionId);
        $this->fillInputs($this->transaction);
        $this->resetValidation();
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }

    public function update()
    {
        $validated = $this->validate();

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->product_id);

            $oldType = $this->transaction->type;
            $oldQty = $this->transaction->quantity;
            $newType = TransactionType::from($this->type);

            $stock = $product->quantity_in_stock;

            // Revert old transaction
            switch ($oldType) {
                case TransactionType::Purchase:
                case TransactionType::Return:
                    $stock -= $oldQty;
                    break;
                case TransactionType::Sale:
                    $stock += $oldQty;
                    break;
                case TransactionType::Adjustment:
                    // In a real adjustment, store the previous stock in notes or another field
                    // You can skip this if you don't support rollback for adjustments
                    break;
            }

            // Apply new transaction
            switch ($newType) {
                case TransactionType::Purchase:
                case TransactionType::Return:
                    $stock += $this->quantity;
                    break;
                case TransactionType::Sale:
                    $stock -= $this->quantity;

                    // ✅ Sale stock check
                    if ($stock < 0) {
                        throw new \Exception('Not enough stock available for this sale.');
                    }
                    break;
                case TransactionType::Adjustment:
                    // ✅ Adjust stock directly to new quantity
                    $stock = $this->quantity;
                    break;
            }

            $product->update([
                'quantity_in_stock' => $stock,
            ]);

            // Update the transaction record
            $this->transaction->update([
                'product_id' => $this->product_id,
                'type' => $newType,
                'quantity' => $this->quantity,
                'notes' => $this->notes,
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'edit-transaction');
            $this->dispatch('transaction-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Transaction updated successfully!'
            );

            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update transaction: ' . $e->getMessage()
            );
        }
    }

    public function delete()
    {
        $this->transaction->delete();

        $this->dispatch('modal-close', name: 'delete-transaction');
        $this->dispatch('modal-close', name: 'edit-transaction');
        $this->dispatch('transaction-deleted');
        $this->dispatch('notify',
            type: 'success',
            message: 'Transaction deleted successfully!'
        );
    }
}
