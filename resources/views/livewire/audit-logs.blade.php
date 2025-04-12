<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-2xl font-bold dark:text-white">Audit Logs</h1>

        <div class="flex flex-col items-center md:flex-row gap-4 w-full md:w-auto">
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <span>Filtering by:</span>

                @php
                    $hasFilters = $search || $userFilter || $actionFilter || $tableFilter || $dateFrom || $dateTo;
                @endphp

                @if($hasFilters)
                    <ul class="inline-block ml-2 space-x-3">
                        @if($search)
                            <li class="inline">Search: <strong>"{{ $search }}"</strong></li>
                        @endif
                        @if($userFilter)
                            <li class="inline">User: <strong>{{ $this->users[$userFilter] ?? $userFilter }}</strong></li>
                        @endif
                        @if($actionFilter)
                            <li class="inline">Action:
                                @php
                                    // Ensure we're working with a string
                                    $actionValue = is_string($actionFilter) ? $actionFilter : ($actionFilter instanceof App\Enums\AuditAction ? $actionFilter->value : '');
                                    $action = $actionValue ? App\Enums\AuditAction::tryFrom($actionValue) : null;
                                @endphp
                                <span class="px-1.5 py-0.5 text-xs rounded-full {{ $action?->color() ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                                    {{ $action?->label() ?? $actionValue }}
                                </span>
                            </li>
                        @endif
                        @if($tableFilter)
                            <li class="inline">Table: <strong>{{ $tableFilter }}</strong></li>
                        @endif
                        @if($dateFrom || $dateTo)
                            <li class="inline">Date:
                                <strong>
                                    {{ $dateFrom ? $dateFrom : 'Start' }}
                                    to
                                    {{ $dateTo ? $dateTo : 'Now' }}
                                </strong>
                            </li>
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
            <div class="relative w-full md:w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search descriptions, record IDs..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
                <div class="absolute left-3 top-2.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Row --}}
    <div class="flex flex-col md:flex-row gap-4 mb-4">
        {{-- Action Filter --}}
        <div class="w-full md:w-48">
            <label for="actionFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Action</label>
            <select
                wire:model.live="actionFilter"
                id="actionFilter"
                class="w-full border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
            >
                <option value="">All Actions</option>
                @foreach($this->availableActions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date Range --}}
        <div class="w-full md:flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">From</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    id="dateFrom"
                    class="w-full border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">To</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    id="dateTo"
                    class="w-full border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                >
            </div>
        </div>

        {{-- Per Page --}}
        <div class="w-full md:w-32">
            <label for="perPage" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Per Page</label>
            <select
                wire:model.live="perPage"
                id="perPage"
                class="w-full border rounded-lg px-3 py-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            Action
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300">
                            Table
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-300" wire:click="setSortBy('created_at')">
                            <button class="flex items-center uppercase">
                                @include('livewire.includes.table-sortable-th', [
                                    'name' => 'created_at',
                                    'displayName' => 'Date'
                                ])
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->logs as $log)
                        <tr class="">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $log->user->name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Ensure we're working with a string
                                    $logAction = is_string($log->action) ? $log->action : ($log->action instanceof App\Enums\AuditAction ? $log->action->value : '');
                                    $action = $logAction ? App\Enums\AuditAction::tryFrom($logAction) : null;
                                @endphp
                                @if($action)
                                    <span class="px-2 py-1 text-xs rounded-full {{ $action->color() }}">
                                        {{ $action->label() }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-white">{{ $log->description }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    Record ID: {{ $log->record_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    wire:click="$set('tableFilter', '{{ $log->table_name }}')"
                                    class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                                >
                                    {{ $log->table_name }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-300">
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-300">
                                No audit logs found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
            {{ $this->logs->links() }}
        </div>
    </div>
</div>
