<div>
    <div class="flex flex-col justify-between items-start mb-4 gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="inline-flex text-4xl font-bold dark:text-white items-center gap-2 whitespace-nowrap">
                Anomalous Transactions
                <flux:modal.trigger name="anomaly-info">
                    <flux:tooltip content="Learn more">
                    <flux:icon.information-circle class="size-8 cursor-pointer" />
                    </flux:tooltip>
                </flux:modal.trigger>
            </h1>

            <div class="flex justify-between items-center">
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <div>
                            <span class="text-zinc-500 dark:text-zinc-400">Total Anomalous Transactions:</span>
                            <span class="font-semibold">{{ $this->totalAnomalousTransactions }}</span>
                        </div>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400 italic">
                            (Review these transactions for potential issues or fraud.)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full">
            <div class="flex flex-col items-center md:flex-row gap-4 w-full">
                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                    <span>Filtering by:</span>

                    @php
                        $hasFilters = $search;
                    @endphp

                    @if($hasFilters)
                        <ul class="inline-block ml-2 space-x-3">
                            @if($search)
                                <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                            @endif
                        </ul>
                    @else
                        <span class="ml-2 text-zinc-500 dark:text-zinc-400">None</span>
                    @endif
                    <button
                        wire:click="clearAllFilters"
                        class="ml-4 text-blue-600 hover:underline"
                    >
                        Clear All Filters
                    </button>
                </div>

                {{-- Search --}}
                <div class="relative w-full md:w-72">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search transactions..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                    >
                    <div class="absolute left-3 top-2.5 text-zinc-400 dark:text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if($search)
                        <div class="absolute right-3 top-2.5 text-zinc-400 dark:text-zinc-300 cursor-pointer" wire:click="$set('search', '')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Per Page --}}
            <select wire:model.live="perPage" class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('id')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'id',
                                    'displayName' => 'Transaction ID'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('amount')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'amount',
                                    'displayName' => 'Amount'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('created_at')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'created_at',
                                    'displayName' => 'Date'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Status</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->anomalousTransactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $transaction->id }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->type }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $transaction->product->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->product->sku ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->amount < 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                            {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-zinc-900 dark:text-white">
                                {{ $transaction->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $transaction->created_at->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->anomalyDetectionResult?->status === 'anomalous')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Anomalous
                                </span>
                                <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                                    {{ $transaction->anomalyDetectionResult?->reason }}
                                </div>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                            No anomalous transactions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->anomalousTransactions->links() }}
        </div>
    </div>

    <flux:modal name="anomaly-info">
        <div class="space-y-6">
            <div class="space-y-2">
                <h2 class="text-2xl font-bold">⚠️ Anomalous Transactions Information</h2>
                <p class="text-muted-foreground">
                    Learn how we detect unusual transaction patterns that may indicate fraud or errors.
                </p>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">How Detection Works</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li>We analyze transactions in real-time using <b>machine learning algorithms</b>.</li>
                    <li>Each transaction is scored based on multiple risk factors including:
                        <ul class="list-disc list-inside ml-5 space-y-1">
                            <li>Transaction amount compared to historical averages</li>
                            <li>Frequency of transactions from same source</li>
                            <li>Time of day and day of week patterns</li>
                            <li>Geographic location anomalies</li>
                            <li>Product purchase combinations</li>
                        </ul>
                    </li>
                    <li>Transactions scoring above a <b>threshold value</b> are flagged as anomalous.</li>
                </ul>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">Technical Overview</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li><b>Algorithm:</b> We use <b>Isolation Forest</b>, an unsupervised learning algorithm effective for anomaly detection.</li>
                    <li><b>Features Analyzed:</b> Amount, frequency, timing, product combinations, customer history, and more.</li>
                    <li><b>Threshold:</b> Adjustable sensitivity based on business requirements.</li>
                </ul>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">Important Notes</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li>Not all anomalies are fraudulent — some may be legitimate but rare events.</li>
                    <li>Regularly review flagged transactions to confirm or dismiss them.</li>
                    <li>The system learns from your decisions to improve accuracy over time.</li>
                    <li>Thresholds can be adjusted based on your risk tolerance.</li>
                </ul>
            </div>
        </div>
    </flux:modal>
</div>
