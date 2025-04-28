<div>
    <div class="flex flex-col justify-between items-start mb-4 gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="inline-flex text-4xl font-bold dark:text-white items-center gap-2 whitespace-nowrap">
                Demand Forecasts
                <flux:modal.trigger name="forecast-info">
                    <flux:tooltip content="Learn more">
                    <flux:icon.information-circle class="size-8 cursor-pointer" />
                    </flux:tooltip>
                </flux:modal.trigger>
            </h1>

            <div class="flex justify-between items-center">
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <span class="text-zinc-500 dark:text-zinc-400">Total:</span>
                        <span class="font-semibold">{{ $this->totalForecasts }}</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400 italic">
                            (Forecasting may take a few minutes. Refresh the page occasionally to see the results.)
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
                        $hasFilters = $search || $productFilter || $dateRangeFilter;
                    @endphp

                    @if($hasFilters)
                        <ul class="inline-block ml-2 space-x-3">
                            @if($search)
                                <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                            @endif
                            @if($productFilter)
                                <li class="inline">Product: <strong>{{ $this->products[$productFilter] ?? $productFilter }}</strong></li>
                            @endif
                            @if($dateRangeFilter)
                                <li class="inline">Date Range: <strong>{{ $this->dateRangeOptions[$dateRangeFilter] ?? $dateRangeFilter }}</strong></li>
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

            {{-- Date Range Filter --}}
            <select wire:model.live="dateRangeFilter" class="w-full md:w-48 border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                @foreach($this->dateRangeOptions as $value => $label)
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

            @can('view', Auth::user())
                @if ($this->canGenerateForecasts())
                    <flux:tooltip content="Generate demand forecasts for all products.">
                        <flux:button icon="play"
                            wire:click="generateForecasts"
                            wire:loading.attr="disabled"
                        >
                            Generate Forecasts
                        </flux:button>
                    </flux:tooltip>
                @else
                    <flux:tooltip content="You can only generate forecasts once every 30 days.">
                        <div>
                            <flux:button icon="play"
                                disabled
                            >
                                Generate Forecasts
                            </flux:button>
                        </div>
                    </flux:tooltip>
                @endif
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('forecast_date')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'forecast_date',
                                    'displayName' => 'Forecast Date'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Product</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('predicted_quantity')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'predicted_quantity',
                                    'displayName' => 'Predicted Qty'
                                ])
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Status</span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            <div class="flex items-center">
                                <span>Days Until</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->forecasts as $forecast)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $forecast->forecast_date->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $forecast->created_at->format('M d, Y') }}
                                <span class="text-zinc-400">(created)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $forecast->product->name }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $forecast->product->sku }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $forecast->predicted_quantity < 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                            {{ number_format($forecast->predicted_quantity, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $isFuture = $forecast->forecast_date > now();
                                $statusClass = $isFuture ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                $statusText = $isFuture ? 'Upcoming' : 'Past';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                            {{ $forecast->forecast_date->diffForHumans() }}
                        </td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                            No demand forecasts found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->forecasts->links() }}
        </div>
    </div>
    <flux:modal name="forecast-info">
        <div class="space-y-6">
            <div class="space-y-2">
                <h2 class="text-2xl font-bold">📈 Demand Forecasting Information</h2>
                <p class="text-muted-foreground">
                    Learn how we predict future sales to support smarter inventory planning.
                </p>
                <p class="text-sm text-muted-foreground italic">
                    <b>Note:</b> Forecasting will officially start <b>next month</b> and can only be run <b>once per month</b>.
                </p>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">How Forecasting Works</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li>We predict product sales for the next <b>30 days</b> using machine learning.</li>
                    <li>A <b>separate model</b> is trained for each product based on its historical sales data.</li>
                    <li>The model learns from patterns such as:
                        <ul class="list-disc list-inside ml-5 space-y-1">
                            <li>Day of the week (weekday vs weekend)</li>
                            <li>Month and season (school season, Christmas, summer)</li>
                            <li>Philippine holidays</li>
                            <li>Recent sales trends (7-day and 30-day averages)</li>
                            <li>Sales behavior over time (7, 14, and 30-day lags)</li>
                        </ul>
                    </li>
                    <li>Only products with <b>at least 60 historical sales records</b> are forecasted.</li>
                </ul>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">Technical Overview</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li><b>Algorithm:</b> We use <b>LightGBM Regressor</b>, a fast and efficient machine learning model.</li>
                    <li><b>Feature Engineering:</b> We create additional inputs such as day and month indicators, holiday flags, seasonality markers, moving averages, and lagged sales values.</li>
                    <li><b>Training Method:</b>
                        <ul class="list-disc list-inside ml-5 space-y-1">
                            <li>80% of the historical data is used for training.</li>
                            <li>20% is reserved for validation (for model tuning and evaluation).</li>
                        </ul>
                    </li>
                    <li><b>Forecasting:</b>
                        <ul class="list-disc list-inside ml-5 space-y-1">
                            <li>We predict one day at a time sequentially.</li>
                            <li>Each newly forecasted day is used as input for predicting the next day.</li>
                        </ul>
                    </li>
                    <li><b>Fallback:</b> If there isn't enough data to split, we train on all available historical data.</li>
                </ul>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-semibold">Important Notes</h3>
                <ul class="list-disc list-inside text-muted-foreground space-y-1">
                    <li>Forecasts are based solely on <b>historical sales patterns</b>.</li>
                    <li>They do <b>not</b> account for unexpected events like promotions, stockouts, or supplier delays.</li>
                    <li>Philippine holidays and seasonality factors are automatically considered.</li>
                    <li>Forecasts are refreshed each time the system runs the forecasting pipeline.</li>
                    <li><b>Forecasting can only be executed once per month</b> to ensure consistency and prevent retraining on partial data.</li>
                </ul>
            </div>
        </div>
    </flux:modal>
</div>
