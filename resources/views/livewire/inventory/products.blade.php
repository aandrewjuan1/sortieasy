<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        @if (session()->has('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if (session()->has('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <h1 class="text-2xl font-bold dark:text-white">Products</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $categoryFilter || $stockFilter;
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($categoryFilter)
                            <li class="inline">Category: <strong>{{ $categoryFilter }}</strong></li>
                        @endif
                        @if($stockFilter)
                            <li class="inline">Stock: <strong>{{ $stockFilter }}</strong></li>
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
                    placeholder="Search products, categories, etc..."
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
                <flux:modal.trigger name="add-product">
                    <flux:button variant="primary">Add Products</flux:button>
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

                        {{-- Category Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Category</span>
                        </th>

                        {{-- SKU Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>SKU</span>
                        </th>

                        {{-- Price Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('price')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'price',
                                    'displayName' => 'Price'
                                ])
                            </button>
                        </th>

                        {{-- Profit Margin Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <span>Margin</span>
                        </th>

                        {{-- Quantity Column --}}
                        <th scopetems-centerpx- class="flex row items-center px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <button class="flex items-center uppercase"  wire:click="setSortBy('quantity_in_stock')">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'quantity_in_stock',
                                    'displayName' => 'Quantity'
                                ])
                            </button>
                            <select wire:model.live="stockFilter" class="ml-2 text-sm border rounded dark:bg-zinc-700 bg-transparent">
                                <option value="">All</option>
                                <option value="low">Low</option>
                                <option value="critical">Critical</option>
                            </select>
                        </th>

                        {{-- Last Restocked Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('last_restocked')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'last_restocked',
                                    'displayName' => 'Last Restocked'
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
                    @forelse($this->products as $product)
                            <tr class="">
                                <flux:modal.trigger name="show-product-modal">
                                    <td class="px-6 py-4 whitespace-nowrap hover:bg-zinc-100 dark:hover:bg-zinc-900 cursor-pointer" wire:click="$dispatch('show-product', { productId: {{ $product->id }} });
                                        $dispatch('modal-show', { name: 'show-product' })" wire:key="product-{{ $product->id }}">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $product->name }}
                                            </div>
                                            @if($product->quantity_in_stock == 0)
                                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Out of Stock
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            {{ Str::limit($product->description, 50) }}
                                        </div>
                                        <div class="text-xs text-blue-600 cursor-pointer mt-2">
                                            Click to view product
                                        </div>
                                    </td>
                                </flux:modal.trigger>

                                {{-- Category --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                    <button
                                        x-cloak
                                        wire:click="$set('categoryFilter', '{{ $product->category }}')"
                                        class="cursor-pointer px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                    >
                                        {{ $product->category }}
                                    </button>
                                </td>

                                {{-- SKU --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-500 dark:text-zinc-300">
                                    {{ $product->sku }}
                                </td>

                                {{-- Price --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                    <div class="font-medium">${{ number_format($product->price, 2) }}</div>
                                    @if($product->cost)
                                        <div class="text-xs text-zinc-400">Cost: ${{ number_format($product->cost, 2) }}</div>
                                    @endif
                                </td>

                                {{-- Profit Margin --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                    @if($product->cost && $product->cost > 0)
                                        @php
                                            $margin = (($product->price - $product->cost) / $product->price) * 100;
                                        @endphp
                                        <span class="{{ $margin >= 50 ? 'text-green-600 dark:text-green-400' : ($margin >= 30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-medium">
                                            {{ number_format($margin, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-zinc-400">N/A</span>
                                    @endif
                                </td>

                                {{-- Quantity --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm {{ $product->quantity_in_stock <= $product->safety_stock ? 'text-red-600 dark:text-red-400 font-bold' : ($product->quantity_in_stock <= $product->reorder_threshold ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-500 dark:text-zinc-300') }}">
                                            {{ $product->quantity_in_stock }}
                                        </span>
                                        @if($product->quantity_in_stock <= $product->reorder_threshold)
                                            @php
                                                $level = $product->quantity_in_stock <= $product->safety_stock ? 'critical' : 'low';
                                                $isCritical = $level === 'critical';
                                                $buttonClasses = $isCritical
                                                    ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800'
                                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800';
                                            @endphp

                                            <button
                                                wire:click='$set("stockFilter", "{{ $level }}")'
                                                class="ml-2 px-2 py-1 text-xs rounded-full {{ $buttonClasses }} cursor-pointer transition-colors duration-200"
                                            >
                                                {{ ucfirst($level) }}
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-xs text-zinc-400 mt-1">
                                        Reorder at: {{ $product->reorder_threshold }}
                                    </div>
                                </td>

                                {{-- Last Restocked --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                    {{ $product->last_restocked ? $product->last_restocked->format('M d, Y') : 'Never' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('edit', $product)
                                            <flux:modal.trigger name="edit-product">
                                                <flux:tooltip content="Edit product">
                                                    <flux:button size="sm" variant="ghost" wire:click="$dispatch('edit-product', { productId: {{ $product->id }} })" icon="pencil-square" />
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        @endcan

                                        <flux:modal.trigger name="add-stocks">
                                            <flux:tooltip content="Add stocks">
                                                <flux:button size="sm" variant="ghost" wire:click="$dispatch('add-stocks', { productId: {{ $product->id }} })" icon="plus" />
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    </div>
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                No products found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->products->links() }}
        </div>
    </div>

    <flux:modal name="show-product" maxWidth="2xl">
        <livewire:inventory.show-product on-load/>
    </flux:modal>

    <flux:modal name="add-product" maxWidth="2xl">
        <livewire:inventory.add-product on-load/>
    </flux:modal>

    <flux:modal name="edit-product" maxWidth="2xl">
        <livewire:inventory.edit-product on-load/>
    </flux:modal>

    <flux:modal name="add-stocks" maxWidth="2xl">
        <livewire:inventory.add-stocks on-load/>
    </flux:modal>
</div>
