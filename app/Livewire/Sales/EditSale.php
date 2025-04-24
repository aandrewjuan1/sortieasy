<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use Livewire\Component;
use App\Enums\SaleChannel;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EditSale extends Component
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
    public $quantityError = '';

    public ?Sale $sale = null;
    public ?Product $product = null;

    public function calculateTotal()
    {
        $this->total_price = round(
            (float)$this->quantity * (float)$this->unit_price,
            2
        );
    }

    #[On('edit-sale')]
    public function editSale($saleId)
    {
        $this->sale = Sale::where('id', $saleId)->first();
        $this->fillInputs($this->sale);
        $this->quantityError = null;
        $this->resetValidation();
    }

    public function fillInputs($sale)
    {
        $this->product_id = $sale->product_id;
        $this->quantity = $sale->quantity;
        $this->total_price = $sale->total_price;
        $this->channel = $sale->channel;
        $this->sale_date = $sale->sale_date->format('Y-m-d');

        // Fetch product details for unit price and stock
        $this->product = Product::find($sale->product_id);
        if ($this->product) {
            $this->unit_price = $this->product->price;
            $this->available_stock = $this->product->quantity_in_stock;
        } else {
            $this->unit_price = 0.00;
            $this->available_stock = 0;
        }
    }

    public function update()
    {
        $this->validate();
        $this->authorize('edit', $this->sale);

        DB::beginTransaction();

        try {
            // Lock product row to prevent race conditions
            $product = Product::where('id', $this->product_id)
                ->lockForUpdate()
                ->first();

            if (!$this->sale) {
                throw new \Exception('Sale record not found.');
            }

            // Restore previous sale quantity to stock first
            $product->increment('quantity_in_stock', $this->sale->quantity);

            if ($this->quantity > $product->quantity_in_stock) {
                $this->quantityError = "Quantity exceeds available stock";
                return;
            }

            // Update the sale
            $this->sale->update([
                'quantity' => $this->quantity,
                'unit_price' => $this->unit_price,
                'total_price' => $this->total_price,
                'channel' => $this->channel,
                'sale_date' => $this->sale_date,
            ]);

            // Decrement new quantity
            $product->decrement('quantity_in_stock', $this->quantity);

            // Update related transaction record if needed (optional)
            $this->sale->transaction?->update([
                'quantity' => $this->quantity,
                'notes' => "Updated Sale #{$this->sale->id} via {$this->channel} channel",
            ]);

            DB::commit();

            $this->dispatch('modal-close', name: 'edit-sale');
            $this->dispatch('sale-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Sale updated successfully! Stock updated.'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:');
            Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update sale.'
            );
        }
    }

    public function delete()
    {
        DB::beginTransaction();

        $this->authorize('delete', $this->sale);

        try {
            if (!$this->sale) {
                throw new \Exception('No sale to delete.');
            }

            // Lock the product to safely update stock
            $product = Product::where('id', $this->sale->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception('Associated product not found.');
            }

            // Revert stock
            $product->increment('quantity_in_stock', $this->sale->quantity);

            // Delete related transaction if it exists
            if ($this->sale->transaction) {
                $this->sale->transaction->delete();
            }

            // Delete the sale itself
            $this->sale->delete();

            DB::commit();

            $this->dispatch('modal-close', name: 'delete-sale');
            $this->dispatch('modal-close', name: 'edit-sale');
            $this->dispatch('sale-deleted');
            $this->dispatch('notify',
                type: 'success',
                message: 'Sale deleted successfully! Stock and transaction reverted.'
            );

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to delete sale.'
            );
        }
    }
}
