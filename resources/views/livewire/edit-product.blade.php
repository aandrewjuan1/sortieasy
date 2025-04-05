<flux:modal name="edit-product" maxWidth="2xl">
   <form wire:submit="updateProduct()">
        <flux:heading size="xl" class="mb-6">Edit Product</flux:heading>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Left Column -->
                <div class="space-y-4">
                    <flux:input
                        label="Product Name"
                        wire:model="name"
                        placeholder="Enter product name"
                        required
                    />

                    <flux:input
                        label="Category"
                        wire:model="category"
                        placeholder="Enter category"
                        required
                    />

                    <flux:input
                        label="SKU"
                        wire:model="sku"
                        placeholder="Enter SKU"
                        required
                    />

                    <flux:select
                        label="Supplier"
                        wire:model="supplier_id"
                        required
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
                        placeholder="0.00"
                        min="0.01"
                        step="0.01"
                        required
                    />

                    <flux:input
                        type="number"
                        label="Cost"
                        wire:model="cost"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                    />

                    <flux:input
                        type="number"
                        label="Quantity in Stock"
                        wire:model="quantity_in_stock"
                        min="0"
                        required
                    />
                </div>
            </div>

            <flux:textarea
                label="Description"
                wire:model="description"
                placeholder="Enter product description"
                rows="3"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    type="number"
                    label="Reorder Threshold"
                    wire:model="reorder_threshold"
                    min="0"
                    required
                />

                <flux:input
                    type="number"
                    label="Safety Stock"
                    wire:model="safety_stock"
                    min="0"
                    required
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
                    <span wire:loading.remove>Update Product</span>
                    <span wire:loading>
                        <flux:icon icon="arrow-path" class="animate-spin h-4 w-4" />
                        Updating...
                    </span>
                </flux:button>
            </div>
        </div>
    </form>
</flux:modal>
