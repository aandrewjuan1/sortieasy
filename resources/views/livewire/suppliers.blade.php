<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Suppliers</h1>

        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            {{-- Search --}}
            <div class="relative w-full md:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search suppliers..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Per Page --}}
            <select wire:model.live="perPage"
                class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        {{-- Name Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <button wire:click="setSortBy('name')" class="flex items-center space-x-1 uppercase">
                                <flux:icon.user variant="solid" class="size-4" />
                                <span>Name</span>
                            </button>
                        </th>

                        {{-- Contact Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center space-x-1">
                                <flux:icon.phone variant="solid" class="size-4" />
                                <span>Contact</span>
                            </div>
                        </th>

                        {{-- Products Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center space-x-1">
                                <flux:icon.cube variant="solid" class="size-4" />
                                <span>Products</span>
                            </div>
                        </th>

                        {{-- Last Delivery Column --}}
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center space-x-1">
                                <flux:icon.inbox-arrow-down variant="solid" class="size-4" />
                                <span>Last Delivery</span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($this->suppliers as $supplier)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{-- Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $supplier->name }}
                                </div>
                            </td>

                            {{-- Contact Info --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <div>{{ $supplier->contact_email }}</div>
                                <div>{{ $supplier->contact_phone }}</div>
                            </td>

                            {{-- Products --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($supplier->products->isNotEmpty())
                                    @foreach($supplier->products as $product)
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product->name }}
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-300">
                                        None
                                    </div>
                                @endif
                            </td>


                            {{-- Latest Delivery --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                @php
                                    $deliveryDate = $supplier->latestDelivery?->delivery_date;
                                @endphp

                                @if ($deliveryDate)
                                    @if (\Carbon\Carbon::parse($deliveryDate)->isToday())
                                        Today
                                    @elseif (\Carbon\Carbon::parse($deliveryDate)->isYesterday())
                                        Yesterday
                                    @else
                                        {{ \Carbon\Carbon::parse($deliveryDate)->diffForHumans() }}
                                    @endif
                                @else
                                    Never
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                No suppliers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $this->suppliers->links() }}
        </div>
    </div>
</div>
