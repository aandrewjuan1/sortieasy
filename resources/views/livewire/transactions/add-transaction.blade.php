<form wire:submit="save" class="p-6">
    <flux:heading size="xl" class="mb-6">Add New Transaction</flux:heading>

    <div class="space-y-6">
        <!-- Product Selection -->
        <flux:field>
            <flux:label badge="Required">Product</flux:label>
            <flux:select
                wire:model="productId"
                placeholder="Select a product"
                required
            >
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
