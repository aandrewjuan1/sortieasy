<div class="bg-white dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Product Summary</h2>
                <a href="{{route('inventory')}}" wire:navigate class="text-blue-600 hover:underline text-sm font-medium dark:text-blue-400">
                    View all products
                </a>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button wire:click="downloadPdf" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </button>
            <div class="flex items-center gap-2 bg-blue-100 dark:bg-blue-900 px-3 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span class="text-sm font-semibold text-indigo-800 dark:text-indigo-200">
                    Total Products: {{ $this->totalProducts }}
                </span>
            </div>
            <div class="flex items-center gap-2 bg-green-100 dark:bg-green-900 px-3 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
                <span class="text-sm font-semibold text-indigo-800 dark:text-indigo-200">
                    Total Stocks: {{ $this->totalStocks }}
                </span>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Low Stock Products -->
        <div class="bg-white dark:bg-zinc-800 border border-red-200 dark:border-red-800 rounded-lg shadow flex flex-col" style="height: 400px;">
            <div class="p-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-red-800 dark:text-red-200">Low Stock</h3>
                    <p class="text-sm text-red-600 dark:text-red-300">{{ $this->lowStockProducts->count() }} products below threshold</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="w-full">
                    <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3 text-right">Threshold</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->lowStockProducts as $product)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                {{ $product->name }}
                            </td>
                            <td class="px-4 py-3 text-right text-red-600 dark:text-red-400 font-medium">{{ $product->quantity_in_stock }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $product->reorder_threshold }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                All products are well stocked
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overstocked Products -->
        <div class="bg-white dark:bg-zinc-800 border border-purple-200 dark:border-purple-800 rounded-lg shadow flex flex-col" style="height: 400px;">
            <div class="p-4 border-b border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <h3 class="font-semibold text-purple-800 dark:text-purple-200">Overstocked</h3>
                    <p class="text-sm text-purple-600 dark:text-purple-300">{{ $this->overstockedProducts->count() }} excess inventory items</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="w-full">
                    <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-right">Current Stock</th>
                            <th class="px-4 py-3 text-right">Threshold</th>
                            <th class="px-4 py-3 text-right">Excess</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->overstockedProducts as $product)
                        <tr class="border-b border-gray-200 dark:border-zinc-700">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                <span class="text-amber-600 dark:text-amber-400">{{ $product->quantity_in_stock }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-gray-500 dark:text-gray-400">
                                @php
                                    $threshold = $product->reorder_threshold * 3; // Using 3x multiplier
                                    if ($product->restockingRecommendation->isNotEmpty()) {
                                        $forecastThreshold = $product->restockingRecommendation->first()->total_forecasted_demand * 1.5;
                                        $threshold = max($threshold, $forecastThreshold);
                                    }
                                @endphp
                                {{ number_format($threshold) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                @php
                                    $excess = $product->quantity_in_stock - $threshold;
                                @endphp
                                <span class="text-red-600 dark:text-red-400">+{{ number_format($excess) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                No overstocked products
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Out of Stock Products -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow flex flex-col" style="height: 400px;">
            <div class="p-4 border-b border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0-12.728l12.728 12.728" />
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Out of Stock</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $this->outOfStockProducts->count() }} products need restocking</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="w-full">
                    <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->outOfStockProducts as $product)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                {{ $product->name }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-medium">0</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                All products in stock
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Critical Stock Products -->
        <div class="bg-white dark:bg-zinc-800 border border-pink-200 dark:border-pink-800 rounded-lg shadow flex flex-col" style="height: 400px;">
            <div class="p-4 border-b border-pink-200 dark:border-pink-800 bg-pink-50 dark:bg-pink-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-pink-800 dark:text-pink-200">Critical Stock</h3>
                    <p class="text-sm text-pink-600 dark:text-pink-300">{{ $this->criticalStockProducts->count() }} products critically low</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="w-full">
                    <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3 text-right">Safety Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->criticalStockProducts as $product)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                {{ $product->name }}
                            </td>
                            <td class="px-4 py-3 text-right text-pink-600 dark:text-pink-400 font-medium">{{ $product->quantity_in_stock }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $product->safety_stock }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                No critical stock alerts
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <flux:modal name="edit-stocks" maxWidth="2xl">
        <livewire:inventory.edit-stocks/>
    </flux:modal>
</div>
