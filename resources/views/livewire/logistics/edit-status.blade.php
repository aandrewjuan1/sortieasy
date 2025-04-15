<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="updateStatus" class="p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between gap-2 pr-8">
            <flux:heading size="xl">Update Delivery Status</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-6">
            <!-- Product (static display) -->
            <flux:field>
                <flux:label>Product</flux:label>
                <flux:input
                    value="{{ $product_name }}"
                    readonly
                    variant="filled"
                />
            </flux:field>

            <!-- Quantity & Delivery Date (static display) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Quantity</flux:label>
                    <flux:input
                        value="{{ $quantity }}"
                        readonly
                        variant="filled"
                    />
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Available stock: {{ $available_stock }}
                    </p>
                </flux:field>

                <flux:field>
                    <flux:label>Delivery Date</flux:label>
                    <flux:input
                        value="{{ $delivery_date }}"
                        readonly
                        variant="filled"
                    />
                </flux:field>
            </div>

            <!-- Editable Delivery Status -->
            <flux:field>
                <flux:radio.group wire:model="status" label="Delivery Status">
                    <flux:radio value="pending" label="Pending" />
                    <flux:radio value="shipped" label="Shipped" />
                    <flux:radio value="delivered" label="Delivered" />
                </flux:radio.group>
                <flux:error name="status" />
            </flux:field>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 flex justify-end">
            <flux:button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                Update Status
            </flux:button>
        </div>
    </form>
</div>
