<div class="bg-white dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Logistics Overview</h2>
                <a href="{{ route('logistics') }}" wire:navigate class="text-blue-600 hover:underline text-sm font-medium dark:text-blue-400">
                    View all shipments
                </a>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="downloadPdf" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </button>
            <div class="flex items-center gap-3 bg-gray-100 dark:bg-zinc-700 px-3 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-300">Last {{ $daysToShow }} days</span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Shipments -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Total Shipments</h3>
            </div>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                {{ number_format($this->totalShipments) }}
            </p>
        </div>

        <!-- Shipment Status Breakdown -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Shipment Status</h3>
            </div>
            <div class="space-y-4">
                @foreach(App\Enums\LogisticStatus::cases() as $status)
                    @php
                        $statusData = $this->shipmentsByStatus[$status->value] ?? ['count' => 0, 'percentage' => 0];
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $status->label() }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $statusData['count'] }} ({{ number_format($statusData['percentage'], 1) }}%)
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200 dark:bg-zinc-600">
                            <div class="h-full rounded-full {{ $status->color() }}"
                                style="width: {{ $statusData['percentage'] }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Late Shipments -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Late Shipments</h3>
            </div>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                {{ $this->lateShipments->count() }}
            </p>
            @if($this->lateShipments->count() > 0)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Oldest is {{ $this->lateShipments->first()->days_late }} days late
                </p>
            @else
                <p class="text-sm text-green-600 dark:text-green-400">
                    All shipments on time!
                </p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Upcoming Deliveries -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Upcoming Deliveries (Next 7 Days)</h3>
            </div>

            <div class="space-y-4">
                @forelse($this->upcomingDeliveries as $delivery)
                    <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <div class="flex items-center gap-3">
                            <span class="{{ $delivery->status->color() }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ $delivery->status->label() }}
                            </span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ $delivery->product->name }} (x{{ $delivery->quantity }})
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @php
                                $days = round($delivery->days_until);
                            @endphp

                            @if($days <= 0)
                                <span class="text-green-600 dark:text-green-400">Today</span>
                            @else
                                in {{ $days }} day{{ $days > 1 ? 's' : '' }}
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No upcoming deliveries in the next 7 days</p>
                @endforelse
            </div>
        </div>

        <!-- Late Deliveries Table -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Late Shipments</h3>
            </div>

            @if($this->lateShipments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-left">Quantity</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Days Late</th>
                                <th class="px-4 py-2 text-left">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->lateShipments as $shipment)
                                <tr class="border-b border-gray-200 dark:border-zinc-600">
                                    <td class="px-4 py-2">{{ $shipment->product->name }}</td>
                                    <td class="px-4 py-2">{{ $shipment->quantity }}</td>
                                    <td class="px-4 py-2">
                                        <span class="{{ $shipment->status->color() }} px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $shipment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-red-600 dark:text-red-400">{{ $shipment->days_late }}</td>
                                    <td class="px-4 py-2">{{ $shipment->delivery_date->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No late shipments! Everything is on track.</p>
            @endif
        </div>
    </div>
</div>
