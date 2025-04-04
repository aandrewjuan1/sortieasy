@props(['title', 'value', 'icon', 'color', 'trend' => null])

<div class="p-4 rounded-2xl shadow {{ $color }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold">{{ $value }}</p>
        </div>
        <x-icon :name="$icon" class="w-8 h-8 text-gray-600" />
    </div>
    @if($trend)
        <div class="mt-2 text-xs {{ $trend === 'positive' ? 'text-green-600' : 'text-red-600' }}">
            {{ $trend === 'positive' ? '↓ Improving' : '↑ Needs attention' }}
        </div>
    @endif
</div>
