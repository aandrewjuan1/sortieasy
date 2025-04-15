<div class="relative" wire:loading.class="opacity-50">
    <form wire:submit="update" class="p-6">
        <div class="mb-6 flex items-center justify-between pr-8">
            <flux:heading size="xl">Edit User</flux:heading>
            <div wire:loading>
                <flux:icon.loading />
            </div>
        </div>

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

            <!-- Password (optional) -->
            <flux:field>
                <flux:label>New Password</flux:label>
                <flux:input
                    wire:model="password"
                    type="password"
                    placeholder="Leave blank to keep current password"
                />
                <flux:error name="password" />
            </flux:field>

            <!-- Confirm Password (optional) -->
            <flux:field>
                <flux:label>Confirm New Password</flux:label>
                <flux:input
                    wire:model="password_confirmation"
                    type="password"
                    placeholder="Confirm new password"
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
            @can('delete', $user)
                <flux:modal.trigger name="delete-user">
                    <flux:button  wire:loading.attr="disabled" variant="danger">Delete User</flux:button>
                </flux:modal.trigger>
            @endcan
            <flux:button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                Update User
            </flux:button>
        </div>
    </form>
    <flux:modal name="delete-user" class="min-w-[22rem]">
        <x-delete-confirm-modal subject="user"/>
    </flux:modal>
</div>
