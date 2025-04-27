<div>
    <div class="flex flex-col justify-between items-start mb-4 gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="text-4xl font-bold dark:text-white">Sales</h1>
            <div class="flex justify-between items-center">
                <div class="flex flex-wrap gap-4">
                    <!-- Revenue Metrics -->
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Revenue:</span>
                        <span class="font-semibold">${{ number_format($this->totalRevenue, 2) }}</span>
                    </div>
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-green-600 dark:text-green-400">Today:</span>
                        <span class="font-semibold">${{ number_format($this->todayRevenue, 2) }}</span>
                    </div>
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-blue-600 dark:text-blue-400">This Week:</span>
                        <span class="font-semibold">${{ number_format($this->thisWeekRevenue, 2) }}</span>
                    </div>
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-purple-600 dark:text-purple-400">This Month:</span>
                        <span class="font-semibold">${{ number_format($this->thisMonthRevenue, 2) }}</span>
                    </div>

                    <!-- Sales Count Metrics -->
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Sales Records:</span>
                        <span class="font-semibold">{{ number_format($this->totalSalesCount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full">
            <div class="flex flex-col items-center md:flex-row gap-4 w-full">
                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                    <span>Filtering by:</span>

                    @php
                        $hasFilters = $search || $channelFilter || $dateFilter;
                    @endphp

                    @if($hasFilters)
                        <ul class="inline-block ml-2 space-x-3">
                            @if($search)
                                <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                            @endif
                            @if($channelFilter)
                                <li class="inline">Channel: <strong>{{ App\Enums\SaleChannel::getLabel($channelFilter) }}</strong></li>
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
                        placeholder="Search products, users..."
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
            </div>
            {{-- Channel Filter --}}
            <select wire:model.live="channelFilter" class="w-full md:w-40 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                @foreach($this->channelOptions as $value => $label)
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

            <flux:modal.trigger name="add-sale">
                <flux:button variant="primary">Add Sale</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('sale_date')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'sale_date',
                                    'displayName' => 'Date'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('channel')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'channel',
                                    'displayName' => 'Channel'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('quantity')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'quantity',
                                    'displayName' => 'Qty'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('unit_price')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'unit_price',
                                    'displayName' => 'Unit Price'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('total_price')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'total_price',
                                    'displayName' => 'Total'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Recorded By</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->sales as $sale)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $sale->sale_date->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $sale->product->name }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $sale->product->sku }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button x-cloak
                                wire:click="$set('channelFilter', '{{ $sale->channel }}')"
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer
                                    {{ App\Enums\SaleChannel::getBgColor($sale->channel->value) }}"
                            >
                                {{ App\Enums\SaleChannel::getLabel($sale->channel->value) }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                            {{ $sale->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                            {{ number_format($sale->unit_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ number_format($sale->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $sale->user->name ?? 'System' }}
                        </td>
                        @can('edit', $sale)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <flux:modal.trigger name="edit-sale">
                                        <flux:tooltip content="Edit sale">
                                            <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-sale', {     saleId: {{ $sale->id }} })" icon="pencil-square" />
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                            No sales found matching your criteria
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->sales->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    <flux:modal name="add-sale" maxWidth="2xl">
        <livewire:sales.add-sale />
    </flux:modal>

    <flux:modal name="edit-sale" maxWidth="2xl">
        <livewire:sales.edit-sale />
    </flux:modal>
</div>
