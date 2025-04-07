<form wire:submit="save" class="p-6">
    <flux:heading size="xl" class="mb-6">Add New Product</flux:heading>

    <div class="space-y-6">
        <!-- Name -->
        <flux:input
            label="Product Name"
            wire:model="name"
            placeholder="Enter product name"
        />

        <!-- Category -->
        <div>
            <flux:input
                label="Category"
                wire:model="category"
                placeholder="Select category"
                list="categories"
            />
            <datalist id="categories">
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </datalist>
        </div>

        <!-- SKU -->
        <div>
            <div class="flex gap-2 items-end">
                <flux:input
                    label="SKU"
                    wire:model="sku"
                    placeholder="Enter SKU"
                    class="flex-1"
                />
                <flux:button
                    type="button"
                    wire:click="generateSKU"
                    size="sm"
                >
                    Generate
                </flux:button>
            </div>
        </div>

        <!-- Price & Cost -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input
                label="Price"
                wire:model="price"
                type="number"
                step="0.01"
                min="0.01"
                placeholder="0.00"
            />
            <flux:input
                label="Cost (Optional)"
                wire:model="cost"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
            />
        </div>

        <!-- Stock Levels -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input
                label="Initial Quantity"
                wire:model="quantity_in_stock"
                type="number"
                min="0"
            />
            <flux:input
                label="Reorder Threshold"
                wire:model="reorder_threshold"
                type="number"
                min="0"
            />
            <flux:input
                label="Safety Stock"
                wire:model="safety_stock"
                type="number"
                min="0"
            />
        </div>


        <!-- Supplier -->
        <flux:select label="Supplier" wire:model="supplier_id">
            <flux:select.option value="">
                Select Supplier
            </flux:select.option>
            @foreach($suppliers as $supplier)
                <flux:select.option value="{{ $supplier->id }}">
                    {{ $supplier->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <!-- Description -->
        <flux:textarea
            label="Description (Optional)"
            wire:model="description"
            placeholder="Enter product description"
            rows="4"
        />
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
            Save Product
        </flux:button>
    </div>
</form>
