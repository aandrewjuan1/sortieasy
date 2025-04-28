<div>
    <div class="flex flex-col justify-between items-start mb-4 gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="inline-flex text-4xl font-bold dark:text-white items-center gap-2 whitespace-nowrap">
                Anomaly Detection
                <flux:tooltip content="Learn more">
                    <flux:icon.exclamation-circle class="size-8 cursor-pointer text-red-600" />
                </flux:tooltip>
            </h1>

            <div class="flex justify-between items-center">
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <div>
                            <span class="text-zinc-500 dark:text-zinc-400">Anomalies Detected:</span>
                            <span class="font-semibold">{{ $this->totalAnomalies }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full">
            <div class="flex flex-col items-center md:flex-row gap-4 w-full">
                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                    <span>Filtering by:</span>

                    @php
                        $hasFilters = $search || $productFilter || !$showOnlyAnomalies;
                    @endphp

                    @if($hasFilters)
                        <ul class="inline-block ml-2 space-x-3">
                            @if($search)
                                <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                            @endif
                            @if($productFilter)
                                <li class="inline">Product: <strong>{{ $this->products[$productFilter] ?? $productFilter }}</strong></li>
                            @endif
                            @if(!$showOnlyAnomalies)
                                <li class="inline">Showing: <strong>All Results</strong></li>
                            @endif
                        </ul>
                    @else
                        <span class="ml-2 text-zinc-500 dark:text-zinc-400">Only Anomalies</span>
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
                        placeholder="Search products..."
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

            {{-- Product Filter --}}
            <select wire:model.live="productFilter" class="w-full md:w-64 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="">All Products</option>
                @foreach($this->products as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>

            {{-- Anomaly Filter --}}
            <select wire:model.live="showOnlyAnomalies" class="w-full md:w-48 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                @foreach($this->filterOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Per Page --}}
            <select wire:model.live="perPage" class="w-full md:w-32 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <div>
        <!-- Header and filters remain the same as before -->

        <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                                Transaction ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('status')">
                                <button class="flex items-center uppercase">
                                    Status
                                </button>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                                Detected At
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                        @forelse($this->results as $result)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                #{{ $result->transaction->id }}
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $result->transaction->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $result->product->name }}
                                    </div>
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $result->product->sku }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($result->status) {
                                        App\Enums\AnomalyStatus::Anomalous->value => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $result->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                {{ $result->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                @if($showOnlyAnomalies)
                                    No anomalies detected
                                @else
                                    No results found
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
                {{ $this->results->links() }}
            </div>
        </div>
    </div>
