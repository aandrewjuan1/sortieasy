<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="updateProduct">
        <div class="flex items-center justify-between pr-6">
            <flux:heading size="xl" class="mb-6">Edit Product</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Left Column -->
                <div class="space-y-4">
                    <flux:input
                        label="Product Name"
                        wire:model="name"
                        wire:loading.attr="disabled"
                        placeholder="Enter product name"
                    />

                    <flux:input
                        label="Category"
                        wire:model="category"
                        wire:loading.attr="disabled"
                        placeholder="Enter category"
                    />

                    <flux:input
                        label="SKU"
                        wire:model="sku"
                        wire:loading.attr="disabled"
                        placeholder="Enter SKU"
                    />

                    <flux:select
                        label="Supplier"
                        wire:model="supplier_id"
                        wire:loading.attr="disabled"
                    >
                        <option value="">Select Supplier</option>
                        @foreach($this->suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <flux:input
                        type="number"
                        label="Price"
                        wire:model="price"
                        wire:loading.attr="disabled"
                        placeholder="0.00"
                        min="0.01"
                        step="0.01"
                    />

                    <flux:input
                        type="number"
                        label="Cost"
                        wire:model="cost"
                        wire:loading.attr="disabled"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                    />

                    <flux:input
                        type="number"
                        label="Quantity in Stock"
                        wire:model="quantity_in_stock"
                        wire:loading.attr="disabled"
                        min="0"
                    />
                </div>
            </div>

            <flux:textarea
                label="Description"
                wire:model="description"
                wire:loading.attr="disabled"
                placeholder="Enter product description"
                rows="3"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    type="number"
                    label="Reorder Threshold"
                    wire:model="reorder_threshold"
                    wire:loading.attr="disabled"
                    min="0"
                />

                <flux:input
                    type="number"
                    label="Safety Stock"
                    wire:model="safety_stock"
                    wire:loading.attr="disabled"
                    min="0"
                />
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button
                    variant="primary"
                    type="submit"
                    wire:loading.attr="disabled"
                >
                    Update Product
                </flux:button>
            </div>
        </div>
    </form>
</div>
