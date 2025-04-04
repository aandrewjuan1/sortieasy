<div>
    <x-layouts.dashboard />

    <div class="grid gap-4">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
                <span>Alerts Summary</span>
            </h2>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    Showing {{ $showResolved ? 'all' : 'unresolved' }} alerts
                </span>
                <button
                    wire:click="toggleResolved"
                    class="flex items-center gap-1 text-sm text-blue-600 hover:underline"
                    wire:loading.attr="disabled"
                >
                    <x-icon name="arrow-path" class="w-4 h-4" />
                    {{ $showResolved ? 'Hide Resolved' : 'Show All' }}
                </button>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card
                title="Total Alerts"
                :value="$this->alertStats['total']"
                icon="bell-alert"
                color="bg-gray-100"
            />
            <x-stat-card
                title="Resolved"
                :value="$this->alertStats['resolved']"
                icon="check-circle"
                color="bg-green-100"
                trend="positive"
            />
            <x-stat-card
                title="Unresolved"
                :value="$this->alertStats['unresolved']"
                icon="x-circle"
                color="bg-red-100"
                trend="negative"
            />
        </div>

        <!-- Low Stock Alerts -->
        <x-alert-section
            title="Low Stock Alerts"
            :alerts="$this->lowStockAlerts"
            type="low_stock"
            icon="exclamation-triangle"
            color="bg-yellow-100"
        />

        <!-- Restock Suggestions -->
        <x-alert-section
            title="Restock Suggestions"
            :alerts="$this->restockSuggestions"
            type="restock_suggestion"
            icon="shopping-cart"
            color="bg-blue-100"
        />
    </div>
</div>
