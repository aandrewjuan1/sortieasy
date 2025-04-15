<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Users Management</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $roleFilter || $statusFilter !== '';
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($roleFilter)
                            <li class="inline">Role: <strong>{{ ucfirst($roleFilter) }}</strong></li>
                        @endif
                        @if($statusFilter !== '')
                            <li class="inline">Status: <strong>{{ $statusFilter === 'active' ? 'Active' : 'Inactive' }}</strong></li>
                        @endif
                    </ul>
                @else
                    <span class="ml-2 text-zinc-500 dark:text-zinc-400">None</span>
                @endif
                <button
                    wire:click="clearAllFilters"
                    class="ml-4 text-blue-600 hover:underline"
                >
                    Clear All Filters
                </button>
            </div>

            {{-- Search --}}
            <div class="relative w-full md:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search users by name, email..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Per Page --}}
            <select wire:model.live="perPage"
                class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>

            @can('create', App\Models\User::class)
                <flux:modal.trigger name="add-user">
                    <flux:button variant="primary">Add User</flux:button>
                </flux:modal.trigger>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        {{-- Name Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('name')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'name',
                                    'displayName' => 'Name'
                                ])
                            </button>
                        </th>

                        {{-- Email Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('email')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'email',
                                    'displayName' => 'Email'
                                ])
                            </button>
                        </th>

                        {{-- Role Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Role</span>
                                <select wire:model.live="roleFilter" class="ml-2 text-sm border rounded dark:bg-zinc-700 bg-transparent">
                                    <option value="">All</option>
                                    <option value="admin">Admin</option>
                                    <option value="employee">Employee</option>
                                </select>
                            </div>
                        </th>

                        {{-- Phone Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Phone</span>
                        </th>

                        {{-- Status Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Status</span>
                                <select wire:model.live="statusFilter" class="ml-2 text-sm border rounded dark:bg-zinc-700 bg-transparent">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </th>

                        {{-- Created At Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('created_at')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'created_at',
                                    'displayName' => 'Joined'
                                ])
                            </button>
                        </th>

                        {{-- Actions Column --}}
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->users as $user)
                        <tr>
                            {{-- Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-500 dark:text-zinc-300">
                                    {{ $user->email }}
                                </div>
                                {{-- @if($user->email_verified_at)
                                    <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        Verified
                                    </div>
                                @else
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                        Unverified
                                    </div>
                                @endif --}}
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button x-cloak
                                    wire:click="$set('roleFilter', '{{ $user->role->value }}')"
                                    class="px-2 py-1 text-xs rounded-full {{ $user->role === App\Enums\UserRole::Admin ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }} hover:bg-opacity-80 transition-colors cursor-pointer"
                                >
                                    {{ $user->role->displayName() }}
                                </button>
                            </td>

                            {{-- Phone --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                {{ $user->phone ?? 'N/A' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @can('changeStatus', $user)
                                    <button
                                        x-cloak
                                        wire:click="toggleStatus({{ $user->id }})"
                                        wire:loading.attr="disabled"
                                        class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }} hover:bg-opacity-80 transition-colors cursor-pointer"
                                    >
                                        <span wire:loading.remove wire:target="toggleStatus({{ $user->id }})">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <flux:icon.loading class="size-4" wire:loading wire:target="toggleStatus({{ $user->id }})"/>
                                    </button>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endcan
                            </td>

                            {{-- Created At --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @can('update', $user)
                                        <flux:modal.trigger name="edit-user">
                                            <flux:tooltip content="Edit user">
                                                <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-user', { userId: {{ $user->id }} })" icon="pencil-square" />
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                No users found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->users->links() }}
        </div>
    </div>

    {{-- Modals --}}
    <flux:modal name="add-user" maxWidth="2xl">
        <livewire:manage-users.add-user />
    </flux:modal>

    <flux:modal name="edit-user" maxWidth="2xl">
        <livewire:manage-users.edit-user />
    </flux:modal>
</div>
