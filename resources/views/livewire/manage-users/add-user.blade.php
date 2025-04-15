<form wire:submit="save" class="p-6">
    <flux:heading size="xl">Add New User</flux:heading>

    <div class="space-y-6">
        <!-- Name -->
        <flux:field>
            <flux:label badge="Required">Full Name</flux:label>
            <flux:input
                wire:model="name"
                placeholder="Enter user's full name"
                required
            />
            <flux:error name="name" />
        </flux:field>

        <!-- Email -->
        <flux:field>
            <flux:label badge="Required">Email</flux:label>
            <flux:input
                wire:model="email"
                type="email"
                placeholder="Enter user's email"
                required
            />
            <flux:error name="email" />
        </flux:field>

        <!-- Password -->
        <flux:field>
            <flux:label badge="Required">Password</flux:label>
                <flux:input
                    wire:model="password"
                    type="password"
                    placeholder="Enter secure password"
                    class="flex-1"
                    required
                />
                <flux:error name="password" />
        </flux:field>

        <!-- Confirm Password -->
        <flux:field>
            <flux:label badge="Required">Confirm Password</flux:label>
            <flux:input
                wire:model="password_confirmation"
                type="password"
                placeholder="Confirm password"
                required
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <!-- Role and Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label badge="Required">Role</flux:label>
                <flux:select wire:model="role" required>
                    <flux:select.option value="{{ App\Enums\UserRole::Employee->value }}">
                        {{ Str::title(App\Enums\UserRole::Employee->value) }}
                    </flux:select.option>
                    @can('changeRole', App\Models\User::class)
                        <flux:select.option value="{{ App\Enums\UserRole::Admin->value }}">
                            {{ Str::title(App\Enums\UserRole::Admin->value) }}
                        </flux:select.option>
                    @endcan
                </flux:select>
                <flux:error name="role" />
            </flux:field>
            <flux:field>
                <flux:label>Account Status</flux:label>
                <flux:checkbox wire:model="is_active">
                    Active (can login)
                </flux:checkbox>
                <flux:error name="is_active" />
            </flux:field>
        </div>

        <!-- Phone -->
        <flux:field>
            <flux:label>Phone Number</flux:label>
            <flux:input
                wire:model="phone"
                type="tel"
                placeholder="Optional contact number"
            />
            <flux:error name="phone" />
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
            Create User
        </flux:button>
    </div>
</form>
