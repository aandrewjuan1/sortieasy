<form wire:submit="save" class="p-6">
    <flux:heading size="xl" class="mb-6">Record New Sale</flux:heading>

    <div class="space-y-6">
        <!-- Product Selection -->
        <flux:field>
            <flux:label badge="Required">Product</flux:label>
        <flux:select
            wire:model.live="product_id"
                required
            >
                <option value=""> Select a product </option>
                @foreach($this->products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                @endforeach
            </flux:select>
            <flux:error name="product_id" />
        </flux:field>

        <!-- Sales Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:field>
                <flux:label badge="Required">Quantity</flux:label>
                <flux:input
                    wire:model="quantity"
                    type="number"
                    min="1"
                    placeholder="0"
                    wire:change="calculateTotal"
                    required
                />
                <p class="text-sm text-gray-500">
                    Available stock: {{ $available_stock }}
                </p>
                <flux:error name="quantity" />
            </flux:field>

            <flux:field>
                <flux:label>Product Price</flux:label>
                <flux:input
                    wire:model="unit_price"
                    type="number"
                    step="0.01"
                    readonly
                    variant="filled"
                />
            </flux:field>

            <flux:field>
                <flux:label>Total Price</flux:label>
                <flux:input
                    wire:model="total_price"
                    type="number"
                    step="0.01"
                    readonly
                    variant="filled"
                />
            </flux:field>
        </div>

        <!-- Sales Channel and Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Sales Channel</flux:label>
                <flux:select
                    wire:model="channel"
                >
                <option value=""> Select channel</option>
                    @foreach(\App\Enums\SaleChannel::cases() as $channel)
                        <option value="{{ $channel->value }}">
                            {{ $channel->label() }}
                        </option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label badge="Required">Sale Date</flux:label>
                <flux:input
                    wire:model="sale_date"
                    type="date"
                    placeholder="Select date"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                />
                <flux:error name="sale_date" />
            </flux:field>
        </div>
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
            Record Sale
        </flux:button>
    </div>
</form>
