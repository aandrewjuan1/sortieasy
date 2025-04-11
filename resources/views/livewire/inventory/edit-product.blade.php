<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update" class="p-6">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit Product</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-6">
            <!-- Name -->
            <flux:field>
                <flux:label badge="Required">Product Name</flux:label>
                <flux:input
                    wire:model="name"
                    placeholder="Enter product name"
                    wire:loading.attr="disabled"
                    required
                />
                <flux:error name="name" />
            </flux:field>

            <!-- Category -->
            <flux:field>
                <flux:label badge="Required">Category</flux:label>
                <div>
                    <flux:input
                        wire:model="category"
                        placeholder="Select category"
                        list="categories"
                        wire:loading.attr="disabled"
                        required
                    />
                    <datalist id="categories">
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </datalist>
                </div>
                <flux:error name="category" />
            </flux:field>

            <!-- SKU -->
            <flux:field>
                <flux:label badge="Required">SKU</flux:label>
                <div class="flex gap-2 items-end">
                    <flux:input
                        wire:model="sku"
                        placeholder="Enter SKU"
                        wire:loading.attr="disabled"
                        class="flex-1"
                        required
                    />
                    <flux:button
                        type="button"
                        wire:click="generateSKU"
                        size="sm"
                        wire:loading.attr="disabled"
                    >
                        Generate
                    </flux:button>
                </div>
                <flux:error name="sku" />
            </flux:field>

            <!-- Price & Cost -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label badge="Required">Price</flux:label>
                    <flux:input
                        wire:model="price"
                        type="number"
                        step="0.01"
                        min="0.01"
                        placeholder="0.00"
                        wire:loading.attr="disabled"
                        required
                    />
                    <flux:error name="price" />
                </flux:field>
                <flux:field>
                    <flux:label>Cost</flux:label>
                    <flux:input
                        wire:model="cost"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        wire:loading.attr="disabled"
                    />
                    <flux:error name="cost" />
                </flux:field>
            </div>

            <!-- Stock Levels -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label badge="Required">Quantity in Stock</flux:label>
                    <flux:input
                        wire:model="quantity_in_stock"
                        type="number"
                        min="0"
                        wire:loading.attr="disabled"
                        required
                    />
                    <flux:error name="quantity_in_stock" />
                </flux:field>
                <flux:field>
                    <flux:label badge="Required">Reorder Threshold</flux:label>
                    <flux:input
                        wire:model="reorder_threshold"
                        type="number"
                        min="0"
                        wire:loading.attr="disabled"
                        required
                    />
                    <flux:error name="reorder_threshold" />
                </flux:field>
                <flux:field>
                    <flux:label badge="Required">Safety Stock</flux:label>
                    <flux:input
                        wire:model="safety_stock"
                        type="number"
                        min="0"
                        wire:loading.attr="disabled"
                        required
                    />
                    <flux:error name="safety_stock" />
                </flux:field>
            </div>

            <!-- Supplier -->
            <flux:field>
                <flux:label badge="Required">Supplier</flux:label>
                <flux:select wire:model="supplier_id" wire:loading.attr="disabled" required>
                    <flux:select.option value="">
                        Select Supplier
                    </flux:select.option>
                    @foreach($suppliers as $supplier)
                        <flux:select.option value="{{ $supplier->id }}">
                            {{ $supplier->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="supplier_id" />
            </flux:field>

            <!-- Description -->
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea
                    wire:model="description"
                    placeholder="Enter product description"
                    rows="4"
                    wire:loading.attr="disabled"
                />
                <flux:error name="description" />
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
                Update Product
            </flux:button>
        </div>
    </form>
</div>
