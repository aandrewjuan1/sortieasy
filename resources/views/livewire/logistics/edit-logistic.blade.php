<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update" class="p-6">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit Logistic</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-6">
            <!-- Product Selection -->
            <flux:field>
                <flux:label badge="Required">Product</flux:label>
                <flux:select
                    wire:model="product_id"
                    required
                >
                <option value="">Select product</option>
                    @foreach($this->products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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

        <div class="mt-6 flex items-center justify-between">
            <flux:modal.trigger name="delete-logistic">
                <flux:button  wire:loading.attr="disabled" variant="danger">Delete Logistic</flux:button>
            </flux:modal.trigger>
            <flux:button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                Update Logistics Entry
            </flux:button>
        </div>
    </form>

    <flux:modal name="delete-logistic" class="min-w-[22rem]">
        <x-delete-confirm-modal subject="logistic"/>
    </flux:modal>
</div>
