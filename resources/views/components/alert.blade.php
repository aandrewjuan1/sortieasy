@props(['type' => 'info', 'message' => session()->get('message')])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-100 text-green-700 dark:border-green-700 dark:bg-green-600/25 dark:text-green-100',
        'error' => 'border-rose-200 bg-rose-100 text-rose-700 dark:border-rose-700 dark:bg-rose-600/25 dark:text-rose-100',
        'warning' => 'border-amber-200 bg-amber-100 text-amber-700 dark:border-amber-700 dark:bg-amber-600/25 dark:text-amber-100',
        'info' => 'border-teal-200 bg-teal-100 text-teal-700 dark:border-teal-700 dark:bg-teal-600/25 dark:text-teal-100',
    ];
    $color = $styles[$type] ?? $styles['info'];
@endphp

@if ($message)
    <div x-data="{ shown: true, timeout: null }"
         x-init="() => { clearTimeout(timeout); timeout = setTimeout(() => { shown = false }, 5000) }"
         x-show="shown"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-6"
         class="fixed bottom-4 right-4 z-50 w-80 p-4 border rounded-lg shadow-md {{ $color }}">
        <p>{{ $message }}</p>
    </div>
@endif
