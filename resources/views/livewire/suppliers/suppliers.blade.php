<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        @if (session()->has('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if (session()->has('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <h1 class="text-2xl font-bold dark:text-white">Suppliers</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $productFilter;
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($productFilter)
                            <li class="inline">Product: <strong>{{ $productFilter }}</strong></li>
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
                    placeholder="Search suppliers..."
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

            @can('view', Auth::user())
                <flux:modal.trigger name="add-supplier">
                    <flux:button variant="primary">Add Supplier</flux:button>
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

                        {{-- Contact Email Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('contact_email')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'contact_email',
                                    'displayName' => 'Email'
                                ])
                            </button>
                        </th>

                        {{-- Contact Phone Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Phone</span>
                        </th>

                        {{-- Address Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Address</span>
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Products Supplied</span>
                            </div>
                        </th>

                        {{-- Created At Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('created_at')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'created_at',
                                    'displayName' => 'Added On'
                                ])
                            </button>
                        </th>

                        @can('view', Auth::user())
                            {{-- Actions Column --}}
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                                <span>Actions</span>
                            </th>
                        @endcan
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->suppliers as $supplier)
                        <tr>
                            {{-- Name --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $supplier->name }}
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                <a href="mailto:{{ $supplier->contact_email }}" class="text-blue-600 hover:underline">
                                    {{ $supplier->contact_email }}
                                </a>
                            </td>

                            {{-- Phone --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                <a href="tel:{{ $supplier->contact_phone }}" class="hover:underline">
                                    {{ $supplier->contact_phone }}
                                </a>
                            </td>

                            {{-- Address --}}
                            <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-300">
                                {{ Str::limit($supplier->address, 30) }}
                            </td>

                            <!-- Products Cell -->
                            <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-300">
                                @if($supplier->products->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($supplier->products as $product)
                                            <button x-cloak
                                                wire:click="$set('productFilter', '{{ $product->name }}')"
                                                class="cursor-pointer px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                            >
                                                {{ $product->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-zinc-400">No products</span>
                                @endif
                                <div class="mt-1 text-xs text-zinc-400">
                                    Total: {{ $supplier->products->count() }}
                                </div>
                            </td>

                            {{-- Created At --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                {{ $supplier->created_at->format('M d, Y') }}
                            </td>

                            {{-- Actions --}}
                            @can('edit', $supplier)
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                            <flux:modal.trigger name="edit-supplier">
                                                <flux:tooltip content="Edit supplier">
                                                    <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-supplier', { supplierId: {{ $supplier->id }} })" icon="pencil-square" />
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                No suppliers found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->suppliers->links() }}
        </div>
    </div>

    <flux:modal name="add-supplier" maxWidth="2xl">
        <livewire:suppliers.add-supplier />
    </flux:modal>

    <flux:modal name="edit-supplier" maxWidth="2xl">
        <livewire:suppliers.edit-supplier />
    </flux:modal>
</div>
