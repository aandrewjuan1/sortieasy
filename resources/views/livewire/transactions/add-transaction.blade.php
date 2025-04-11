<form wire:submit="save" class="p-6" x-data="{ showAdjustmentReason: false }"
      x-on:transaction-type-updated.window="showAdjustmentReason = ($event.detail === 'adjustment')">
    <flux:heading size="xl" class="mb-6">Add New Transaction</flux:heading>

    <div class="space-y-6">
        <!-- Product Selection -->
        <flux:field>
            <flux:label badge="Required">Product</flux:label>
            <flux:select
                wire:model="product_id"
                required
            >
                <option value="">Select Product</option>
                @foreach($this->products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="product_id" />
        </flux:field>

        <!-- Transaction Type and Quantity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label badge="Required">Transaction Type</flux:label>
                <flux:select
                    wire:model="type"
                    x-on:change="$dispatch('transaction-type-updated', $event.target.value)"
                    required
                >
                    <option value="">Select type</option>
                    <option value="purchase">Purchase</option>
                    <option value="sale">Sale</option>
                    <option value="return">Return</option>
                    <option value="adjustment">Adjustment</option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label badge="Required">Quantity</flux:label>
                <flux:input
                    wire:model="quantity"
                    type="number"
                    placeholder="Enter quantity"
                    required
                />
                <flux:error name="quantity" />
            </flux:field>
        </div>

        <!-- Adjustment Reason (Conditional) -->
        <div x-show="showAdjustmentReason" x-transition>
            <flux:field>
                <flux:label badge="Required">Adjustment Reason</flux:label>
                <flux:select
                    wire:model="adjustment_reason"
                    required
                >
                    <option value="">Select reason</option>
                    <option value="damaged">Damaged Goods</option>
                    <option value="lost">Lost/Missing</option>
                    <option value="donation">Donation/Gift</option>
                    <option value="stock_take">Stock Take Correction</option>
                    <option value="other">Other Reason</option>
                </flux:select>
                <flux:error name="adjustment_reason" />
            </flux:field>
        </div>

        <!-- Notes -->
        <flux:field>
            <flux:label>Notes</flux:label>
            <flux:textarea
                wire:model="notes"
                placeholder="Additional information about this transaction"
                rows="3"
            />
            <flux:error name="notes" />
        </flux:field>
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button
            type="submit"
            variant="primary"
            wire:loading.attr="disabled"
        >
            Record Transaction
        </flux:button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('transactionTypeUpdated', (type) => {
            window.dispatchEvent(new CustomEvent('transaction-type-updated', {
                detail: type
            }));
        });
    });
</script>
@endpush
