<form wire:submit="save" class="p-6">
    <flux:heading size="xl" class="mb-6">Add Logistics Entry</flux:heading>

    <div class="space-y-6">
        <!-- Product Selection -->
        <flux:field>
            <flux:label badge="Required">Product</flux:label>
            <flux:select
                wire:model.live="product_id"
                required
            >
            <option value="">Select product</option>
                @foreach($this->products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
            </flux:select>
            <flux:error name="product_id" />
        </flux:field>

        <!-- Quantity and Delivery Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label badge="Required">Quantity</flux:label>
                <flux:input
                    wire:model="quantity"
                    type="number"
                    min="1"
                    placeholder="Enter quantity"
                    required
                />
                <p class="text-sm text-gray-500">
                    Available stock: {{ $available_stock }}
                </p>
                <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400" data-flux-error>
                    @if ($quantityError)
                        <flux:icon icon="exclamation-triangle" variant="mini" class="inline" />
                        {{ $quantityError }}
                    @endif
                </div>
                <flux:error name="quantity" />
            </flux:field>

            <flux:field>
                <flux:label badge="Required">Delivery Date</flux:label>
                <flux:input
                    wire:model="delivery_date"
                    type="date"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                />
                <flux:error name="delivery_date" />
            </flux:field>
        </div>

        <!-- Status -->
        <flux:field>
            <flux:radio.group wire:model="status" label="Delivery Status">
                <flux:radio value="pending" label="Pending" checked />
                <flux:radio value="shipped" label="Shipped" />
                <flux:radio value="delivered" label="Delivered" />
            </flux:radio.group>
            <flux:error name="status" />
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
            Save Logistics Entry
        </flux:button>
    </div>
</form>
