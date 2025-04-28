<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>

        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group class="grid">

                    <flux:separator />

                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard*')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navlist.item>



                    <flux:navlist.item icon="archive-box" :href="route('inventory')" :current="request()->routeIs('inventory')" wire:navigate>
                        {{ __('Inventory') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="truck" :href="route('suppliers')" :current="request()->routeIs('suppliers')" wire:navigate>
                        {{ __('Suppliers') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="presentation-chart-line" :href="route('sales')" :current="request()->routeIs('sales')" wire:navigate>
                        {{ __('Sales') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="clipboard-document-check" :href="route('transactions')" :current="request()->routeIs('transactions')" wire:navigate>
                        {{ __('Transactions') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="globe-alt" :href="route('logistics')" :current="request()->routeIs('logistics')" wire:navigate>
                        {{ __('Logistics') }}
                    </flux:navlist.item>

                    <flux:separator />

                    <!-- Predictive Tools Section -->
                    <div class="px-3 pt-2 pb-1 text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">
                        {{ __('ML FEATURES') }}
                    </div>

                    <flux:navlist.item icon="rectangle-group" :href="route('restocking-recommendations')" :current="request()->routeIs('restocking-recommendations')" wire:navigate>
                        <div class="flex items-center gap-2">
                            {{ __('Restock Recommends') }}
                        </div>
                    </flux:navlist.item>

                    <flux:navlist.item icon="cursor-arrow-ripple" :href="route('forecasts')" :current="request()->routeIs('forecasts')" wire:navigate>
                        <div class="flex items-center gap-2">
                            {{ __('Demand Forecasts') }}
                        </div>
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>



            <flux:spacer />

            @can('view', Auth::user())
                <flux:navlist variant="outline">
                    <!-- Admin-specific links -->
                        <div class="px-3 pt-2 pb-1 text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">
                            {{ __('ADMIN ONLY') }}
                        </div>
                        <flux:navlist.item icon="bug-ant" :href="route('anomalous-transactions')" :current="request()->routeIs('anomalous-transactions')" wire:navigate>{{ __('Anomalous Transactions') }}</flux:navlist.item>

                        <flux:navlist.item icon="users" :href="route('manage-users')" :current="request()->routeIs('manage-users')" wire:navigate>{{ __('Manage Users') }}</flux:navlist.item>

                        <flux:navlist.item icon="newspaper" :href="route('audit-logs')" :current="request()->routeIs('audit-logs')" wire:navigate>{{ __('Audit Logs') }}</flux:navlist.item>
                        <flux:separator />
                </flux:navlist>
            @endcan




            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}
        <x-notify />
        @fluxScripts

    </body>
</html>
