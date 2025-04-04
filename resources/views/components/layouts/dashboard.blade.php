<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl mb-5">
    <flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar scrollable>
            <flux:navbar.item
                icon="cube"
                :href="route('dashboard.product-summary')"
                wire:navigate
                :active="request()->routeIs('dashboard.product-summary')">
                Product Summary
            </flux:navbar.item>

            <flux:navbar.item
                icon="users"
                :href="route('dashboard.supplier-overview')"
                wire:navigate
                :active="request()->routeIs('dashboard.supplier-overview')">
                Supplier Overview
            </flux:navbar.item>

            <flux:navbar.item
                icon="banknotes"
                :href="route('dashboard.transaction-summary')"
                wire:navigate
                :active="request()->routeIs('dashboard.transaction-summary')">
                Transaction Summary
            </flux:navbar.item>

            <flux:navbar.item
                icon="bell-alert"
                :href="route('dashboard.alert-summary')"
                wire:navigate
                :active="request()->routeIs('dashboard.alert-summary')">
                Alerts Summary
            </flux:navbar.item>

            <flux:navbar.item
                icon="clock"
                :href="route('dashboard.recent-activity')"
                wire:navigate
                :active="request()->routeIs('dashboard.recent-activity')">
                Recent Activity
            </flux:navbar.item>
        </flux:navbar>
    </flux:header>

    <div class="flex-1 px-6 pt-4 self-stretch">
        <flux:heading size="xl" level="1">Good day, {{ auth()->user()->name}}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{ $slot }}
</div>
