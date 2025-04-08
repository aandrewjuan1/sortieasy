<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="updateStock">
        <div class="flex items-center justify-between pr-6">
            <flux:heading size="xl" class="mb-6">Edit Stocks</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-4">
            <flux:input
                type="number"
                label="Quantity in Stock"
                wire:model="quantity_in_stock"
                wire:loading.attr="disabled"
                min="0"
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
                    Update Stock
                </flux:button>
            </div>
        </div>
    </form>
</div>
