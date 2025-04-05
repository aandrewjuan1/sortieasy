<div>
    <x-layouts.dashboard />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Recent Transactions Card -->
        <div class="p-4 bg-white dark:bg-gray-800 shadow rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <x-icon name="document-text" class="w-5 h-5" />
                    <span>Recent Transactions</span>
                </h2>
                <span class="text-sm text-gray-500">Last {{ $recentTransactionsLimit }}</span>
            </div>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                @forelse($this->recentTransactions as $transaction)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $transaction->formatted_type }} —
                                    <span class="text-blue-600 dark:text-blue-400">{{ $transaction->quantity }}</span> units
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Product: {{ $transaction->product->name ?? 'N/A' }}
                                    @if($transaction->user)
                                        <span class="ml-2">by {{ $transaction->user->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $transaction->formatted_date }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        <p>No recent transactions found.</p>
                    </div>
                @endforelse
            </div>
            <div class="px-6 py-3 text-right">
                <a href="{{route('transactions')}}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    View all transactions &rarr;
                </a>
            </div>
        </div>

        <!-- Transaction Volume Card -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 shadow rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <x-icon name="chart-bar" class="w-5 h-5" />
                    <span>Transaction Volume</span>
                </h2>
                <span class="text-sm text-blue-600 dark:text-blue-400">Last {{ $daysToShow }} days</span>
            </div>

            <ul class="space-y-3">
                @foreach($this->transactionTypes as $type)
                    @php
                        $volume = $this->transactionVolume[$type] ?? 0;
                        $colorClass = match($type) {
                            'purchase' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                            'sale' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                            'return' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                        };
                    @endphp

                    <li class="flex items-center justify-between">
                        <span class="capitalize">{{ str_replace('_', ' ', $type) }}</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                            {{ number_format($volume) }}
                        </span>
                    </li>
                @endforeach
            </ul>

            @if(array_sum($this->transactionVolume) === 0)
                <div class="mt-4 text-center text-sm text-blue-600 dark:text-blue-400">
                    No transactions recorded in this period.
                </div>
            @endif
        </div>
    </div>
</div>
