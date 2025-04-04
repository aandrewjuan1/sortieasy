<div>
    <x-layouts.dashboard />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-4">
        <!-- Summary Card -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-900">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-500 dark:text-gray-300">Total Suppliers</h3>
                    <p class="text-2xl font-bold dark:text-white">{{ $this->totalSuppliers }}</p>
                </div>
            </div>
        </div>

        <!-- Top Suppliers Card -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <h3 class="text-lg font-medium mb-4 dark:text-white">🏆 Top Suppliers</h3>
            <div class="space-y-3">
                @foreach($this->topSuppliers as $supplier)
                    <div class="flex justify-between items-center">
                        <span class="font-medium dark:text-gray-200">{{ $supplier->name }}</span>
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">
                            {{ $supplier->products_count }} products
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 md:col-span-2">
            <h3 class="text-lg font-medium mb-4 dark:text-white">📦 Recent Deliveries</h3>
            <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                @forelse($this->recentDeliveries as $delivery)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 p-2 bg-green-100 rounded-full dark:bg-green-900">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $delivery->product->name }}
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ Carbon::parse($delivery->delivery_date)->diffForHumans() }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                From {{ $delivery->product->supplier->name ?? 'N/A' }}
                                • Qty: {{ $delivery->quantity }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No recent deliveries found.</p>
                @endforelse
            </div>
        </div>

        <!-- All Suppliers Card -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 md:col-span-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium dark:text-white">📇 All Suppliers</h3>
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search suppliers..."
                        class="pl-8 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        wire:model.live.debounce.300ms="search"
                    >
                    <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Last Delivery</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($this->suppliersWithProductCount as $supplier)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $supplier->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div>{{ $supplier->contact_email }}</div>
                                    <div>{{ $supplier->contact_phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $supplier->products_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    @if($supplier->latestDelivery)
                                        {{ \Carbon\Carbon::parse($supplier->latestDelivery->delivery_date)->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
