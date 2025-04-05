<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Suppliers</h1>

        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
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
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300" wire:click="setSortBy('name')">
                            <button class="flex items-center uppercase">
                                <flux:icon.user class="size-4 mr-2"/>
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'name',
                                    'displayName' => 'Name'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-medium text-gray-500 tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.phone class="size-4 mr-2"/>
                                <span>Contact</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-medium text-gray-500 tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.cube class="size-4 mr-2"/>
                                <span>product count</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs uppercase font-medium text-gray-500 tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.inbox-arrow-down class="size-4 mr-2"/>
                                <span>Last Delivery</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($this->suppliers as $supplier)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $supplier->contact_email }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $supplier->contact_phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $supplier->products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @if($supplier->latestDelivery && $supplier->latestDelivery->delivery_date)
                                @php
                                    $logisticDate = \Carbon\Carbon::parse($supplier->latestDelivery->delivery_date);
                                    $diffInDays = (int)floor($logisticDate->diffInDays());

                                    if ($logisticDate->isToday()) {
                                        echo 'Today';
                                    } elseif ($logisticDate->isYesterday()) {
                                        echo 'Yesterday';
                                    } else {
                                        echo $diffInDays . ' days ago';
                                    }
                                @endphp
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

        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $this->suppliers->links() }}
        </div>
    </div>
</div>
