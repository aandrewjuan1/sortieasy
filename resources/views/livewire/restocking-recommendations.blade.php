<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="inline-flex text-3xl sm:text-4xl font-bold dark:text-white items-center gap-2 whitespace-nowrap">
                Restocking Recommendations
                <flux:modal.trigger name="restocking-info">
                    <flux:icon.information-circle class="size-8 cursor-pointer" />
                </flux:modal.trigger>
            </h1>

            <flux:modal name="restocking-info">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-bold">Recommendation Overview</h2>
                        <p>Automated restocking suggestions based on demand forecasts, current inventory levels, and lead times. Designed to optimize inventory turnover while preventing stockouts.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold">Algorithm Details</h2>
                        <ul class="list-disc list-inside">
                            <li>Considers 30-day demand forecasts with safety buffers</li>
                            <li>Accounts for supplier lead times and order cycles</li>
                            <li>Adjusts for seasonal variations and trends</li>
                            <li>Incorporates current stock levels and in-transit inventory</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold">Key Metrics</h2>
                        <p>Reorder quantities are calculated to cover demand until next expected delivery plus safety stock. Thresholds are dynamically adjusted based on forecast accuracy and variability.</p>
                    </div>
                </div>
            </flux:modal>

            <div class="flex justify-between items-center">
                <div class="flex flex-wrap gap-4">
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Products:</span>
                        <span class="font-semibold">{{ $this->totalProducts }}</span>
                    </div>
                    <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-yellow-600 dark:text-yellow-400">Total Recommendations:</span>
                        <span class="font-semibold">{{ $this->productsWithRecommendations }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                @if($search)
                    <span>Filtering by search: <strong>"{{ $search }}"</strong></span>
                    <button
                        wire:click="$set('search', '')"
                        class="ml-4 text-blue-600 hover:underline"
                    >
                        Clear
                    </button>
                @else
                    <span>No filters applied</span>
                @endif
            </div>



            {{-- Search --}}
            <div class="relative w-full md:w-72">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
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

            {{-- Per Page --}}
            <select wire:model.live="perPage" class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('name')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'name',
                                    'displayName' => 'Product'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Forecast</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Inventory</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Thresholds</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Safety Stock</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Recommendation</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->products as $product)
                        @if($product->restockingRecommendations->isNotEmpty())
                            @php $recommendation = $product->restockingRecommendations->first(); @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer"
                                wire:click="$dispatch('edit-stocks', { productId: {{ $product->id }} });
                                $dispatch('modal-show', { name: 'edit-stocks' })"
                                wire:key="product-{{ $product->id }}">
                                {{-- Product Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $product->name }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $product->sku }}
                                    </div>
                                </td>

                                {{-- Forecast Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                        {{ number_format($recommendation->total_forecasted_demand, 0) }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        Projected: {{ number_format($recommendation->projected_stock, 0) }}
                                    </div>
                                </td>

                                {{-- Inventory Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $recommendation->quantity_in_stock < $product->safety_stock ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                                        {{ $recommendation->quantity_in_stock }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        @if($recommendation->quantity_in_stock < $product->safety_stock)
                                            <span class="text-red-500">Below safety stock</span>
                                        @else
                                            {{ round(($recommendation->quantity_in_stock / $product->safety_stock) * 100) }}% of safety
                                        @endif
                                    </div>
                                </td>

                                {{-- Thresholds Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400">Current:</span>
                                            <span class="font-medium">{{ $product->reorder_threshold }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400">Suggested:</span>
                                            <span class="font-medium {{ $product->suggested_reorder_threshold > $product->reorder_threshold ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-900 dark:text-white' }}">
                                                {{ $product->suggested_reorder_threshold ?? '-' }}
                                                @if($product->suggested_reorder_threshold && $product->suggested_reorder_threshold != $product->reorder_threshold)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Safety Stock Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400">Current:</span>
                                            <span class="font-medium">{{ $product->safety_stock }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400">Suggested:</span>
                                            <span class="font-medium {{ $product->suggested_safety_stock > $product->safety_stock ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-900 dark:text-white' }}">
                                                {{ $product->suggested_safety_stock ?? '-' }}
                                                @if($product->suggested_safety_stock && $product->suggested_safety_stock != $product->safety_stock)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Recommendation Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-yellow-700 dark:text-yellow-300">
                                        {{ number_format($recommendation->reorder_quantity, 0) }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        @if($recommendation->reorder_quantity > 0)
                                            Suggested order
                                        @else
                                            No action needed
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-400 dark:text-zinc-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2a4 4 0 014-4h5a2 2 0 012 2v4a2 2 0 01-2 2h-5a4 4 0 01-4-4z" />
                                    </svg>
                                    <p class="text-lg font-medium text-zinc-600 dark:text-zinc-400">No restocking recommendations found</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-500 mt-1">All products appear to be adequately stocked</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->products->links() }}
        </div>
    </div>

    <flux:modal name="edit-stocks" maxWidth="2xl">
        <livewire:inventory.edit-stocks/>
    </flux:modal>
</div>
