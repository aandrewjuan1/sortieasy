<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Logistics</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $statusFilter;
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($statusFilter)
                            <li class="inline">Status: <strong>{{ $statusFilter }}</strong></li>
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
                    placeholder="Search logistics..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Status Filter --}}
            <select wire:model.live="statusFilter" class="w-full md:w-40 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="">All Statuses</option>
                @foreach($this->statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Per Page --}}
            <select wire:model.live="perPage"
                class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>

            @can('view', Auth::user())
                <flux:modal.trigger name="add-logistic">
                    <flux:button variant="primary">Add Logistic</flux:button>
                </flux:modal.trigger>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        {{-- Product Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('product.name')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'product.name',
                                    'displayName' => 'Product'
                                ])
                            </button>
                        </th>

                        {{-- Quantity Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('quantity')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'quantity',
                                    'displayName' => 'Quantity'
                                ])
                            </button>
                        </th>

                        {{-- Delivery Date Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('delivery_date')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'delivery_date',
                                    'displayName' => 'Delivery Date'
                                ])
                            </button>
                        </th>

                        {{-- Status Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Status</span>
                        </th>

                        {{-- Days Remaining Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Time Status</span>
                        </th>

                        {{-- Actions Column --}}
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->logistics as $logistic)
                        <tr class="">
                            {{-- Product --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $logistic->product->name }}
                                    </div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    SKU: {{ $logistic->product->sku }}
                                </div>
                            </td>

                            {{-- Quantity --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                <span class="font-medium {{ $logistic->quantity > 100 ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                                    {{ $logistic->quantity }}
                                </span>
                            </td>

                            {{-- Delivery Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-500 dark:text-zinc-300">
                                {{ $logistic->delivery_date->format('M d, Y') }}
                                <div class="text-xs text-zinc-400 mt-1">
                                    {{ $logistic->delivery_date->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button x-cloak
                                    wire:click="$set('statusFilter', '{{ $logistic->status }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer {{ $logistic->status->color() }} hover:{{ $logistic->status->colorHover() }}"
                                >
                                    {{ $logistic->status->label() }}
                                </button>
                            </td>

                            {{-- Time Status --}}
                            @php
                                $timeStatus = $this->getTimeStatus($logistic);
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $timeStatus['class'] }}">
                                {{ $timeStatus['display'] }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class-center justify-end space-x-2">
                                    @can('edit', $logistic)
                                        <flux:modal.trigger name="edit-logistic">
                                            <flux:tooltip content="Edit logistic">
                                                <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-logistic', { logisticId: {{ $logistic->id }} })" icon="pencil-square" />
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    @endcan
                                    @can('editStatus', $logistic)
                                        <flux:modal.trigger name="edit-status">
                                            <flux:tooltip content="Edit status">
                                                <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-status', { logisticId: {{ $logistic->id }} })" icon="archive-box-arrow-down" />
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                No logistics records found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->logistics->links() }}
        </div>
    </div>

    <flux:modal name="edit-logistic" maxWidth="2xl">
        <livewire:logistics.edit-logistic on-load/>
    </flux:modal>

    <flux:modal name="edit-status" maxWidth="2xl">
        <livewire:logistics.edit-status on-load/>
    </flux:modal>

    <flux:modal name="add-logistic" maxWidth="2xl">
        <livewire:logistics.add-logistic />
    </flux:modal>
</div>
