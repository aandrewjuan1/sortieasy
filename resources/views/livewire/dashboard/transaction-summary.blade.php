<div class="p-6 bg-white rounded-lg shadow dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Transaction Summary</h2>
        </div>
        <div class="flex items-center gap-3 bg-gray-100 dark:bg-zinc-700 px-3 py-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-sm text-gray-600 dark:text-gray-300">Last {{ $this->daysToShow }} days</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Transaction Volume -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Transaction Volume</h3>
            </div>
            <div class="space-y-4">
                @foreach(App\Enums\TransactionType::cases() as $type)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $type->label() }}
                        </span>
                        <span class="text-sm font-semibold {{ $this->getTransactionTextColor($type->value) }}">
                            {{ $this->transactionVolume[$type->value] ?? 0 }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                        <div class="{{ $this->getTransactionBgColor($type->value) }} h-2.5 rounded-full"
                              style="width: {{ $this->getPercentage($type->value) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Transaction Types Breakdown -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Transaction Types</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach(App\Enums\TransactionType::cases() as $type)
                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-zinc-700">
                    <div class="p-2 rounded-full {{ $this->getTransactionIconBgColor($type->value) }}">
                        @switch($type->value)
                            @case('purchase')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                @break
                            @case('sale')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                                @break
                            @case('return')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                @break
                            @case('adjustment')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                @break
                        @endswitch
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type->label() }}</p>
                        <p class="text-lg font-bold {{ $this->getTransactionTextColor($type->value) }}">
                            {{ $this->transactionVolume[$type->value] ?? 0 }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700 rounded-t-lg flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Recent Transactions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Latest {{ $this->recentTransactionsLimit }} activities</p>
            </div>
        </div>
        <div class="overflow-y-auto" style="max-height: 500px;">
            <table class="w-full">
                <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->recentTransactions as $transaction)
                    <tr class="border-b border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $transaction->product->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $transaction->user->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type->color() }} dark:{{ $this->getDarkModeColor($transaction->type->value) }}">
                                {{ $transaction->type->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium {{ $this->getTransactionQtyColor($transaction->type->value) }}">
                            {{ $transaction->quantity }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                            {{ $transaction->formatted_date }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            No transactions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
