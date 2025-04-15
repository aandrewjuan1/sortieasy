<div class="relative" wire:loading.class="opacity-50"
    x-data="{
        type: @entangle('type'),
        get showAdjustmentReason() {
            return this.type === 'adjustment';
        },
        init() {
            // Watch for type changes
            this.$watch('type', (value) => {
                if (value !== 'adjustment') {
                    @this.set('adjustment_reason', null);
                    @this.resetVal('adjustment_reason');
                }
                @this.resetVal();
            });
        }
    }">
    <form wire:submit="update" class="p-6">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit Transaction</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-6">
            <!-- Product Display (readonly but still bound via hidden input) -->
            <flux:field>
                <flux:label>Product</flux:label>

                <!-- Show product name -->
                <flux:input
                    value="{{ $product?->name }}"
                    readonly
                    variant="filled"
                />

                <!-- Hidden input to retain wire:model binding for product_id -->
                <input type="hidden" wire:model="product_id">
            </flux:field>

            <!-- Transaction Type and Quantity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label badge="Required">Transaction Type</flux:label>
                    <flux:select
                        wire:model="type"
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
                    <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400" data-flux-error>
                        @if ($quantityError)
                            <flux:icon icon="exclamation-triangle" variant="mini" class="inline" />
                            {{ $quantityError }}
                        @endif
                    </div>
                    <flux:error name="quantity" />
                </flux:field>
            </div>

            <!-- Adjustment Reason (Conditional) -->
            <div x-show="showAdjustmentReason" x-transition>
                <flux:field>
                    <flux:label badge="Required">Adjustment Reason</flux:label>
                    <flux:select
                        wire:model="adjustment_reason"
                        x-bind:required="showAdjustmentReason"
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

        <div class="mt-6 flex items-center justify-between">
            @can('delete',$this->transaction)
                <flux:modal.trigger name="delete-transaction">
                    <flux:button wire:loading.attr="disabled" variant="danger">Delete Transaction</flux:button>
                </flux:modal.trigger>
            @else
                <!-- This empty div will push the update button to the right when delete is visible -->
                <div></div>
            @endcan
            <flux:button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                Update Transaction
            </flux:button>
        </div>
    </form>

    <flux:modal name="delete-transaction" class="min-w-[22rem]">
        <x-delete-confirm-modal subject="transaction"/>
    </flux:modal>
</div>
