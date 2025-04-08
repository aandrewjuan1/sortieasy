<div class="flex h-full w-full flex-1 flex-col gap-4 mb-5">
    <flux:header class="block! bg-white lg:bg-zinc-50 rounded-xl dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 shadow-md">
        <flux:navbar scrollable class="flex space-x-6 py-2">
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
                icon="currency-dollar"
                :href="route('dashboard.sale-summary')"
                wire:navigate
                :active="request()->routeIs('dashboard.sale-summary')">
                Sale Summary
            </flux:navbar.item>

            <flux:navbar.item
                icon="bell-alert"
                :href="route('dashboard.alert-summary')"
                wire:navigate
                :active="request()->routeIs('dashboard.alert-summary')">
                Alert Summary
            </flux:navbar.item>
        </flux:navbar>
    </flux:header>
    {{ $slot }}
</div>
