<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Transactions</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $typeFilter || $dateFilter;
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($typeFilter)
                            <li class="inline">Type: <strong>{{ $typeFilter }}</strong></li>
                        @endif
                        @if($dateFilter)
                            <li class="inline">Date: <strong>{{ $dateFilter }}</strong></li>
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
            <div class="relative w-full md:w-72">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search products, users, notes..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-zinc-400 dark:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                @if($search)
                    <div class="absolute right-3 top-2.5 text-zinc-400 dark:text-zinc-300 cursor-pointer" wire:click="$set('search', '')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                @endif
            </div>

            <select wire:model.live="typeFilter" class="w-full md:w-40 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="">All Types</option>
                @foreach($this->types as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Date Filter --}}
            <select wire:model.live="dateFilter" class="w-full md:w-40 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                @foreach($this->dateFilterOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Per Page --}}
            <select wire:model.live="perPage" class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>

            <flux:modal.trigger name="add-transaction">
                <flux:button variant="primary">Add Transaction</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('created_at')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'created_at',
                                    'displayName' => 'Date'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Type</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('quantity')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'quantity',
                                    'displayName' => 'Quantity'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Recorded By</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Notes</span>
                            </div>
                        </th>

                        {{-- Actions Column --}}
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->transactions as $transaction)
                    <tr class="">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $transaction->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $transaction->product->name }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->product->sku }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button x-cloak
                                wire:click="$set('typeFilter', '{{ $transaction->type }}')"
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer {{ $transaction->type->color() }} hover:{{ $transaction->type->colorHover() }}"
                            >
                                {{ $transaction->type->label() }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium {{ $transaction->quantity < 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                                {{ $transaction->quantity }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $transaction->user->name ?? 'System' }}
                        </td>

                        <td class="px-6 py-4 max-w-xs">
                            @if($transaction->notes)
                                <div x-data="{ expanded: false }" class="text-sm text-zinc-900 dark:text-white">
                                    <div x-show="!expanded" class="truncate">
                                        {{ Str::limit($transaction->notes, 100) }}
                                    </div>
                                    <div x-show="expanded" x-cloak>
                                        {{ $transaction->notes }}
                                    </div>
                                    <button @click="expanded = !expanded" class="text-xs text-blue-600 hover:underline mt-1">
                                        <span x-show="!expanded">More</span>
                                        <span x-show="expanded">Less</span>
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">No notes</span>
                            @endif
                        </td>


                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <flux:modal.trigger name="edit-transaction">
                                    <flux:tooltip content="Edit transaction">
                                        <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-transaction', { transactionId: {{ $transaction->id }} })" icon="pencil-square" />
                                    </flux:tooltip>
                                </flux:modal.trigger>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                            No transactions found matching your criteria
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->transactions->links() }}
        </div>
    </div>

    <flux:modal name="add-transaction" maxWidth="2xl">
        <livewire:transactions.add-transaction />
    </flux:modal>

    <flux:modal name="edit-transaction" maxWidth="2xl">
        <livewire:transactions.edit-transaction />
    </flux:modal>
</div>
