<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit Supplier</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

        <div class="space-y-6">
            <!-- Name -->
            <flux:field>
                <flux:label badge="Required">Supplier Name</flux:label>
                <flux:input
                    wire:model="name"
                    placeholder="Enter supplier name"
                    required
                />
                <flux:error name="name" />
            </flux:field>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label badge="Required">Contact Email</flux:label>
                    <flux:input
                        wire:model="contact_email"
                        type="email"
                        placeholder="supplier@example.com"
                        required
                    />
                    <flux:error name="contact_email" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Required">Contact Phone</flux:label>
                    <flux:input
                        wire:model="contact_phone"
                        type="tel"
                        placeholder="(555) 555-5555"
                        mask="(999) 999-9999"
                        maxlength="15"
                        required
                    />
                    <flux:error name="contact_phone" />
                </flux:field>
            </div>

            <!-- Address -->
            <flux:field>
                <flux:label badge="Required">Address</flux:label>
                <flux:textarea
                    wire:model="address"
                    placeholder="Enter full supplier address"
                    rows="4"
                    required
                />
                <flux:error name="address" />
            </flux:field>
        </div>

        <div class="mt-6 flex items-center justify-between">
            @can('delete', $supplier)
                <flux:modal.trigger name="delete-supplier">
                    <flux:button variant="danger">Delete Product</flux:button>
                </flux:modal.trigger>
            @endcan
            <flux:modal name="delete-supplier" class="min-w-[22rem]">
                <x-delete-confirm-modal subject="supplier"/>
            </flux:modal>
            <div class="flex justify-end gap-4">
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    Update Supplier
                </flux:button>
            </div>
        </div>
    </form>
</div>
