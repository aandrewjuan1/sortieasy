<div>
    <x-layouts.dashboard />
    <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Product Summary</h2>
            <div class="flex space-x-4">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-200">Total Products</p>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ $this->totalProducts }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200">Total Stock</p>
                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">{{ $this->totalStock }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Low Stock Products -->
            <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 rounded-lg shadow">
                <div class="p-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 rounded-t-lg">
                    <h3 class="font-semibold text-red-800 dark:text-red-200">Low Stock (Below Reorder Threshold)</h3>
                    <p class="text-sm text-red-600 dark:text-red-300">{{ $this->lowStockProducts->count() }} products need attention</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Current Stock</th>
                                <th class="px-4 py-2 text-right">Threshold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->lowStockProducts as $product)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                                <td class="px-4 py-2 text-right text-red-600 dark:text-red-400">{{ $product->quantity_in_stock }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ $product->reorder_threshold }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No low stock products</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Safety Stock Alerts -->
            <div class="bg-white dark:bg-gray-800 border border-orange-200 dark:border-orange-800 rounded-lg shadow">
                <div class="p-4 border-b border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 rounded-t-lg">
                    <h3 class="font-semibold text-orange-800 dark:text-orange-200">Safety Stock Alerts</h3>
                    <p class="text-sm text-orange-600 dark:text-orange-300">{{ $this->safetyStockProducts->count() }} critical products</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Current Stock</th>
                                <th class="px-4 py-2 text-right">Safety Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->safetyStockProducts as $product)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                                <td class="px-4 py-2 text-right text-orange-600 dark:text-orange-400">{{ $product->quantity_in_stock }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ $product->safety_stock }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No safety stock alerts</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Overstocked Products -->
            <div class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-lg shadow">
                <div class="p-4 border-b border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/20 rounded-t-lg">
                    <h3 class="font-semibold text-purple-800 dark:text-purple-200">Overstocked Products</h3>
                    <p class="text-sm text-purple-600 dark:text-purple-300">{{ $this->overstockedProducts->count() }} products with excess inventory</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Current Stock</th>
                                <th class="px-4 py-2 text-right">Threshold x2</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->overstockedProducts as $product)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                                <td class="px-4 py-2 text-right text-purple-600 dark:text-purple-400">{{ $product->quantity_in_stock }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ $product->reorder_threshold * 2 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No overstocked products</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Restock Recommendations -->
            <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800 rounded-lg shadow">
                <div class="p-4 border-b border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg">
                    <h3 class="font-semibold text-blue-800 dark:text-blue-200">Restock Recommendations</h3>
                    <p class="text-sm text-blue-600 dark:text-blue-300">{{ $this->restockRecommendations->count() }} products suggested for restock</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Current Stock</th>
                                <th class="px-4 py-2 text-right">Recommended Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->restockRecommendations as $product)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                                <td class="px-4 py-2 text-right text-blue-600 dark:text-blue-400">{{ $product->quantity_in_stock }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">
                                    {{ max($product->safety_stock, $product->reorder_threshold) - $product->quantity_in_stock }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No restock recommendations</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
