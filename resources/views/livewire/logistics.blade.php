<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        @if (session()->has('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if (session()->has('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        <h1 class="text-2xl font-bold dark:text-white">Logistics</h1>

        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search logistics..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <select wire:model.live="statusFilter" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.cube class="size-4 mr-2"/>
                                <span>Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300" wire:click="setSortBy('name')">
                            <button class="flex items-center uppercase">
                                <flux:icon.numbered-list class="size-4 mr-2"/>
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'quantity',
                                    'displayName' => 'Quantity'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300" wire:click="setSortBy('name')">
                            <button class="flex items-center uppercase">
                                <flux:icon.calendar class="size-4 mr-2"/>
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'delivery_date',
                                    'displayName' => 'Delivery Date'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.command-line class="size-4 mr-2"/>
                                <span>Status</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <div class="flex items-center">
                                <flux:icon.calendar-days class="size-4 mr-2"/>
                                <span>Days Remaining</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($this->logistics as $logistic)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $logistic->product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $logistic->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $logistic->delivery_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $logistic->status->color() }}">
                                {{ $logistic->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @php
                                // Skip calculation if already delivered
                                if ($logistic->status === \App\Enums\LogisticStatus::Delivered) {
                                    $timeDisplay = 'Delivered';
                                    $colorClass = 'text-gray-600 dark:text-gray-300';
                                } else {
                                    $diff = now()->diff($logistic->delivery_date);
                                    $isPast = now() > $logistic->delivery_date;
                                    $days = $diff->d;
                                    $hours = $diff->h;

                                    if ($days == 0 && $hours == 0) {
                                        $timeDisplay = 'Due now';
                                        $colorClass = 'text-yellow-600 dark:text-yellow-400';
                                    } elseif (!$isPast) {
                                        $timeDisplay =
                                            ($days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') . ' ' : '') .
                                            ($hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '') .
                                            ' remaining';
                                        $colorClass = 'text-green-600 dark:text-green-400';
                                    } else {
                                        $timeDisplay =
                                            ($days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') . ' ' : '') .
                                            ($hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' : '') .
                                            'overdue';
                                        $colorClass = 'text-red-600 dark:text-red-400';
                                    }
                                }
                            @endphp

                            <span class="{{ $colorClass }}">
                                {{ $timeDisplay }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                            No logistics records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $this->logistics->links() }}
        </div>
    </div>
</div>
