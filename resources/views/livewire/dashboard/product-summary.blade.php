<div>
    <x-layouts.dashboard />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-4">
        <!-- Stock Overview Card -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    📦
                </div>
                <h2 class="text-xl font-bold">Stock Overview</h2>
            </div>
            <div class="space-y-2">
                <p class="flex justify-between">
                    <span>Total Products:</span>
                    <strong class="text-blue-600">{{ $this->totalProducts }}</strong>
                </p>
                <p class="flex justify-between">
                    <span>Total In Stock:</span>
                    <strong class="text-blue-600">{{ number_format($this->totalStock) }}</strong>
                </p>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-yellow-50 shadow-lg rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    ⚠️
                </div>
                <h2 class="text-xl font-semibold">Low Stock ({{ $this->lowStockProducts->count() }})</h2>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($this->lowStockProducts as $product)
                    <a href=""
                       class="block p-3 hover:bg-yellow-100 rounded-lg transition-colors">
                        <div class="flex justify-between items-center">
                            <span class="truncate">{{ $product->name }}</span>
                            <span class="text-yellow-700 font-medium">
                                {{ $product->quantity_in_stock }} / {{ $product->reorder_threshold }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-yellow-500 h-1.5 rounded-full"
                                 style="width: {{ min(100, ($product->quantity_in_stock / $product->reorder_threshold) * 100) }}%"></div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">✅ All products within stock limits</p>
                @endforelse
            </div>
        </div>

        <!-- Safety Stock Card -->
        <div class="bg-red-50 shadow-lg rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-red-100 rounded-lg">
                    🚨
                </div>
                <h2 class="text-xl font-semibold">Below Safety Stock ({{ $this->safetyStockProducts->count() }})</h2>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($this->safetyStockProducts as $product)
                    <a href=""
                       class="block p-3 hover:bg-red-100 rounded-lg transition-colors">
                        <div class="flex justify-between items-center">
                            <span class="truncate">{{ $product->name }}</span>
                            <span class="text-red-700 font-medium">
                                {{ $product->quantity_in_stock }} / {{ $product->safety_stock }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-red-500 h-1.5 rounded-full"
                                 style="width: {{ min(100, ($product->quantity_in_stock / $product->safety_stock) * 100) }}%"></div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">✅ All products above safety stock</p>
                @endforelse
            </div>
        </div>

        <!-- Overstocked Card -->
        <div class="bg-green-50 shadow-lg rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    📈
                </div>
                <h2 class="text-xl font-semibold">Overstocked ({{ $this->overstockedProducts->count() }})</h2>
            </div>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($this->overstockedProducts as $product)
                    <a href=""
                       class="block p-3 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="flex justify-between items-center">
                            <span class="truncate">{{ $product->name }}</span>
                            <span class="text-green-700 font-medium">
                                {{ $product->quantity_in_stock }} (Threshold: {{ $product->reorder_threshold * 2 }})
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">🔄 Inventory levels optimal</p>
                @endforelse
            </div>
        </div>

        <!-- Restock Recommendations Card -->
        <div class="bg-blue-50 shadow-lg rounded-xl p-6 md:col-span-2 xl:col-span-3">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    🛒
                </div>
                <h2 class="text-xl font-semibold">Restock Recommendations ({{ $this->restockRecommendations->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($this->restockRecommendations as $product)
                    <div class="p-3 bg-white rounded-lg border border-blue-100 hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-center mb-1">
                            <span class="truncate font-medium">{{ $product->name }}</span>
                            <span class="text-sm {{ $product->quantity_in_stock < $product->safety_stock ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $product->quantity_in_stock }} in stock
                            </span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Safety: {{ $product->safety_stock }}</span>
                            <span>Reorder: {{ $product->reorder_threshold }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full"
                                 style="width: {{ min(100, ($product->quantity_in_stock / max($product->safety_stock, $product->reorder_threshold)) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-4">
                        ✅ No restock recommendations at this time
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
