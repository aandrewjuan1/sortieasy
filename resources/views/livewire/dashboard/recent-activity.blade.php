<div class="space-y-6">
    <x-layouts.dashboard />

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Showing {{ $period === 'week' ? 'this week\'s' : ('last ' . $period) }} activity
                    </p>
                </div>

                <div class="w-full sm:w-48">
                    <label for="period" class="sr-only">Select Period</label>
                    <select
                        id="period"
                        wire:model.live="period"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md transition-colors duration-200"
                    >
                        <option value="week">Last Week</option>
                        <option value="month">Last Month</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <div wire:loading.remove wire:target="period" class="transition-opacity duration-200">
                @if($this->recentTransactions->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No activity found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            No transactions in the selected period.
                        </p>
                    </div>
                @else
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach ($this->recentTransactions as $transaction)
                            <li class="py-4 hover:bg-gray-50/50 transition-colors duration-150 px-2 -mx-2 rounded">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->type->value === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $transaction->type->label() }}
                                            </span>
                                            @if($transaction->product)
                                                <span class="text-sm text-gray-500 truncate">
                                                    {{ $transaction->product->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            @if($transaction->user)
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $transaction->user->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $transaction->formatted_quantity }} units
                                        </div>
                                        <div class="flex items-center mt-1 text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <time datetime="{{ $transaction->created_at->toDateTimeString() }}">
                                                {{ $transaction->created_at_for_humans }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Loading State -->
            <div wire:loading.flex wire:target="period" class="justify-center py-12">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Loading transactions...</p>
                </div>
            </div>
        </div>

        @unless($this->recentTransactions->isEmpty())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 text-right">
            <a href="" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                View all transactions &rarr;
            </a>
        </div>
        @endunless
    </div>
</div>
