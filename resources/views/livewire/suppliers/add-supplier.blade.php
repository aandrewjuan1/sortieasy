<form wire:submit="save" class="p-6">
    <flux:heading size="xl" class="mb-6">Add New Supplier</flux:heading>

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

    <div class="mt-6 flex justify-end gap-4">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button
            type="submit"
            variant="primary"
            wire:loading.attr="disabled"
        >
            Save Supplier
        </flux:button>
    </div>
</form>
