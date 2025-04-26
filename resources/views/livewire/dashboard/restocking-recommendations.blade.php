<div class="bg-white dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <!-- Restocking Recommendations -->
    <div class="bg-white dark:bg-zinc-800 border border-yellow-200 dark:border-yellow-800 rounded-lg shadow flex flex-col mb-6">
        <div class="p-4 border-b border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20 rounded-t-lg flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h5a2 2 0 012 2v4a2 2 0 01-2 2h-5a4 4 0 01-4-4z" />
            </svg>
            <div>
                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Restocking Recommendations</h3>
                <p class="text-sm text-yellow-600 dark:text-yellow-300">
                    {{ $this->products->filter(fn($p) => $p->restockingRecommendations->isNotEmpty())->count() }} products to review
                </p>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                    <strong>Note:</strong> The restocking recommendations are generated based on a supervised learning algorithm or demand prediction script to forecast inventory needs.
                </p>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="w-full">
                <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-right">Forecast</th>
                        <th class="px-4 py-3 text-right">In Stock</th>
                        <th class="px-4 py-3 text-right">Projected</th>
                        <th class="px-4 py-3 text-right">Current Threshold</th>
                        <th class="px-4 py-3 text-right">Suggested Threshold</th>
                        <th class="px-4 py-3 text-right">Current Safety</th>
                        <th class="px-4 py-3 text-right">Suggested Safety</th>
                        <th class="px-4 py-3 text-right">Reorder Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->products->filter(fn($p) => $p->restockingRecommendations->isNotEmpty()) as $product)
                        @php $recommendation = $product->restockingRecommendations->first(); @endphp
                        <tr class="cursor-pointer border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-zinc-700" wire:click="$dispatch('edit-stocks', { productId: {{ $product->id }} });
                            $dispatch('modal-show', { name: 'edit-stocks' })" wire:key="product-{{ $product->id }}">
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                <span class="block">{{ $product->name }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $product->sku }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-yellow-600 dark:text-yellow-400">
                                {{ number_format($recommendation->total_forecasted_demand, 0) }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                {{ $recommendation->quantity_in_stock }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($recommendation->projected_stock, 0) }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                {{ $product->reorder_threshold }}
                            </td>
                            <td class="px-4 py-3 text-right @if($product->suggested_reorder_threshold > $product->reorder_threshold) text-yellow-600 dark:text-yellow-400 font-medium @else text-gray-600 dark:text-gray-400 @endif">
                                {{ $product->suggested_reorder_threshold ?? '-' }}
                                @if($product->suggested_reorder_threshold && $product->suggested_reorder_threshold != $product->reorder_threshold)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                {{ $product->safety_stock }}
                            </td>
                            <td class="px-4 py-3 text-right @if($product->suggested_safety_stock > $product->safety_stock) text-yellow-600 dark:text-yellow-400 font-medium @else text-gray-600 dark:text-gray-400 @endif">
                                {{ $product->suggested_safety_stock ?? '-' }}
                                @if($product->suggested_safety_stock && $product->suggested_safety_stock != $product->safety_stock)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-yellow-800 dark:text-yellow-300 font-semibold">
                                {{ number_format($recommendation->reorder_quantity, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                No restocking recommendations at the moment
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <flux:modal name="edit-stocks" maxWidth="2xl">
        <livewire:inventory.edit-stocks/>
    </flux:modal>
</div>
