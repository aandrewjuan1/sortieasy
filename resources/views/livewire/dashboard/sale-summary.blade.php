<div class="bg-white dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Sale Summary</h2>
                <a href="{{route('sales')}}" wire:navigate class="text-blue-600 hover:underline text-sm font-medium dark:text-blue-400">
                    View all sales
                </a>
            </div>
        </div>

    </div>

    <!-- Revenue Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Total Revenue</h3>
            </div>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                ${{ number_format($this->totalRevenue, 2) }}
            </p>
        </div>

        <!-- Sales Volume -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Sales Volume</h3>
            </div>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                {{ number_format(array_sum(array_column($this->salesByChannel, 'count'))) }}
            </p>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Avg. Order Value</h3>
            </div>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                @php
                    $totalCount = array_sum(array_column($this->salesByChannel, 'count'));
                    $avgValue = $totalCount > 0 ? $this->totalRevenue / $totalCount : 0;
                @endphp
                ${{ number_format($avgValue, 2) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Sales Bar Chart</h3>
            </div>
            <div class="h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        @script
        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const darkMode = Flux.dark;

            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: @js($this->chartData),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            backgroundColor: darkMode ? '#374151' : '#ffffff',
                            titleColor: darkMode ? '#f3f4f6' : '#111827',
                            bodyColor: darkMode ? '#e5e7eb' : '#1f2937',
                            borderColor: darkMode ? '#4b5563' : '#d1d5db',
                            borderWidth: 1,
                            usePointStyle: true,
                            callbacks: {
                                label(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += context.datasetIndex === 0
                                        ? `${context.raw} sales`
                                        : `$${context.raw.toLocaleString()}`;
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            align: 'center',
                            labels: {
                                boxWidth: 12,
                                padding: 16,
                                usePointStyle: true,
                                font: {
                                    family: 'Inter, sans-serif',
                                    size: 12
                                },
                                color: darkMode ? '#e5e7eb' : '#1f2937'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: darkMode ? '#9ca3af' : '#6b7280',
                                callback(value) {
                                    return Number.isInteger(value) ? value : null;
                                }
                            },
                            grid: {
                                color: darkMode ? 'rgba(156, 163, 175, 0.1)' : 'rgba(209, 213, 219, 0.5)'
                            }
                        },
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        </script>
        @endscript


        <!-- Revenue by Channel -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Revenue by Channel</h3>
                <flux:spacer/>
                <div class="flex items-center gap-3 bg-gray-100 dark:bg-zinc-700 px-3 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-300">Last {{ $daysToShow }} days</span>
                </div>
            </div>
            <div class="space-y-4">
                @foreach(App\Enums\SaleChannel::cases() as $channel)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ App\Enums\SaleChannel::getLabel($channel->value) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ${{ number_format($this->salesByChannel[$channel->value]['revenue'], 2) }}
                        </span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-200 dark:bg-zinc-600">
                        <div class="h-full rounded-full"
                             style="width: {{ $this->totalRevenue > 0 ? ($this->salesByChannel[$channel->value]['revenue'] / $this->totalRevenue) * 100 : 0 }}%;
                                    background-color: {{ $channel->getTextColor($channel->value) }};">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sales by Channel (Count) -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l5 5m0 0l5-5m-5 5v12" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Sales by Channel (Count)</h3>
                <flux:spacer/>
                <div class="flex items-center gap-3 bg-gray-100 dark:bg-zinc-700 px-3 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-300">Last {{ $daysToShow }} days</span>
                </div>
            </div>
            <div class="space-y-4">
                @foreach(App\Enums\SaleChannel::cases() as $channel)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $channel->getLabel($channel->value) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $this->salesByChannel[$channel->value]['count'] }}
                        </span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-200 dark:bg-zinc-600">
                        <div class="h-full rounded-full" style="width: {{ $this->totalVolume != 0 ? ($this->salesByChannel[$channel->value]['count'] / $this->totalVolume) * 100 : 0 }}%; background-color: {{ App\Enums\SaleChannel::getTextColor($channel->value) }};"></div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>

     <!-- Recent Sales Table -->
     <div class="bg-white dark:bg-zinc-800 mb-6 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
        <div class="flex items-center gap-3 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5" />
            </svg>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">Recent Sales</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-gray-700 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-left">Recorded By</th>
                        <th class="px-4 py-2 text-left">Quantity</th>
                        <th class="px-4 py-2 text-left">Unit Price</th>
                        <th class="px-4 py-2 text-left">Total Price</th>
                        <th class="px-4 py-2 text-left">Channel</th>
                        <th class="px-4 py-2 text-left">Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->recentSales as $sale)
                        <tr class="border-b border-gray-200 dark:border-zinc-600">
                            <td class="px-4 py-2">{{ $sale->product->name }}</td>
                            <td class="px-4 py-2">{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $sale->quantity }}</td>
                            <td class="px-4 py-2">${{ number_format($sale->unit_price, 2) }}</td>
                            <td class="px-4 py-2">${{ number_format($sale->total_price, 2) }}</td>

                            <!-- Channel with dynamic styles based on SaleChannel Enum -->
                            <td class="px-4 py-2">
                                <button x-cloak
                                    wire:click=""
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer
                                        {{ App\Enums\SaleChannel::getBgColor($sale->channel->value) }}"
                                >
                                    {{ App\Enums\SaleChannel::getLabel($sale->channel->value) }}
                                </button>
                            </td>

                            <td class="px-4 py-2">{{ $sale->sale_date->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
