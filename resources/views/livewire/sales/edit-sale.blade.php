<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update" class="p-6">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit Sale</flux:heading>
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

        <div class="mt-6 flex items-center justify-between">
            <flux:modal.trigger name="delete-sale">
                <flux:button  wire:loading.attr="disabled" variant="danger">Delete Sale</flux:button>
            </flux:modal.trigger>
            <div class="flex justify-end gap-4">
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    Update Sale
                </flux:button>
            </div>
        </div>
    </form>
    <flux:modal name="delete-sale" class="min-w-[22rem]">
        <x-delete-confirm-modal subject="sale"/>
    </flux:modal>
</div>
