<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update" class="p-6">
        <flux:heading size="xl" class="mb-6">Edit Transaction</flux:heading>

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
                        min="1"
                        placeholder="Enter quantity"
                        required
                    />
                    <flux:error name="quantity" />
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
            <flux:modal.trigger name="delete-transaction">
                <flux:button  wire:loading.attr="disabled" variant="danger">Delete Transaction</flux:button>
            </flux:modal.trigger>
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
