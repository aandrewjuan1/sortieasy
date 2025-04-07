<div class="p-6 bg-white rounded-lg shadow dark:bg-zinc-800">
    <x-layouts.dashboard/>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Alert Summary</h2>
        </div>

        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <button wire:click="toggleResolved" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-md shadow-sm text-sm font-medium bg-white dark:bg-zinc-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none">
                        {{ $showResolved ? 'Hide Resolved' : 'Show Resolved' }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="bg-gray-100 dark:bg-zinc-700 p-3 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Total Alerts</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $this->alertStats['total'] }}</p>
                    </div>
                </div>
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="text-sm text-red-800 dark:text-red-200">Unresolved</p>
                        <p class="text-xl font-bold text-red-800 dark:text-red-200">{{ $this->alertStats['unresolved'] }}</p>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="text-sm text-green-800 dark:text-green-200">Resolved</p>
                        <p class="text-xl font-bold text-green-800 dark:text-green-200">{{ $this->alertStats['resolved'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Critical Alerts -->
        <div class="bg-white dark:bg-zinc-800 border border-red-200 dark:border-red-800 rounded-lg shadow flex flex-col" style="height: 320px;">
            <div class="p-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-red-800 dark:text-red-200">Critical Alerts</h3>
                    <p class="text-sm text-red-600 dark:text-red-300">{{ $this->criticalAlerts->count() }} urgent issues</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($this->criticalAlerts as $alert)
                <div class="border-b border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ $alert->product->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="{{ $alert->type->color() }} px-2 py-0.5 rounded-full text-xs">
                                    {{ $alert->type->label() }}
                                </span>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($alert->resolved)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Resolved
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Unresolved
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $alert->message }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $alert->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    No critical alerts
                </div>
                @endforelse
            </div>
        </div>

        <!-- High Alerts -->
        <div class="bg-white dark:bg-zinc-800 border border-orange-200 dark:border-orange-800 rounded-lg shadow flex flex-col" style="height: 320px;">
            <div class="p-4 border-b border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-orange-800 dark:text-orange-200">High Priority Alerts</h3>
                    <p class="text-sm text-orange-600 dark:text-orange-300">{{ $this->highAlerts->count() }} important issues</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($this->highAlerts as $alert)
                <div class="border-b border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ $alert->product->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="{{ $alert->type->color() }} px-2 py-0.5 rounded-full text-xs">
                                    {{ $alert->type->label() }}
                                </span>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($alert->resolved)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Resolved
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    Unresolved
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $alert->message }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $alert->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    No high priority alerts
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Medium Alerts -->
        <div class="bg-white dark:bg-zinc-800 border border-yellow-200 dark:border-yellow-800 rounded-lg shadow flex flex-col" style="height: 320px;">
            <div class="p-4 border-b border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Medium Priority Alerts</h3>
                    <p class="text-sm text-yellow-600 dark:text-yellow-300">{{ $this->mediumAlerts->count() }} warnings</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($this->mediumAlerts as $alert)
                <div class="border-b border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ $alert->product->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="{{ $alert->type->color() }} px-2 py-0.5 rounded-full text-xs">
                                    {{ $alert->type->label() }}
                                </span>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($alert->resolved)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Resolved
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Unresolved
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $alert->message }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $alert->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    No medium priority alerts
                </div>
                @endforelse
            </div>
        </div>

        <!-- Low Alerts -->
        <div class="bg-white dark:bg-zinc-800 border border-blue-200 dark:border-blue-800 rounded-lg shadow flex flex-col" style="height: 320px;">
            <div class="p-4 border-b border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-t-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-800 dark:text-blue-200">Low Priority Alerts</h3>
                    <p class="text-sm text-blue-600 dark:text-blue-300">{{ $this->lowAlerts->count() }} notifications</p>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($this->lowAlerts as $alert)
                <div class="border-b border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ $alert->product->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="{{ $alert->type->color() }} px-2 py-0.5 rounded-full text-xs">
                                    {{ $alert->type->label() }}
                                </span>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($alert->resolved)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Resolved
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Unresolved
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $alert->message }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $alert->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    No low priority alerts
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
