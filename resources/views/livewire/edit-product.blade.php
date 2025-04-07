<flux:modal name="edit-product" maxWidth="2xl">
    <div class="relative min-h-[500px] min-w-[500px]"> <!-- Added min-height for loading state -->
        <!-- Loading Spinner (shown while product is loading) -->
        <div wire:loading.flex class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 dark:bg-zinc-800 dark:bg-opacity-80">
            <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <form wire:submit="updateProduct()" wire:loading.remove>
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
                            Updating...
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>
