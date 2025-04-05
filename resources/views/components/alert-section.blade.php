@props(['title', 'alerts', 'type', 'icon', 'color'])

<div class="{{ $color }} p-4 rounded-2xl shadow">
    <h3 class="font-semibold mb-3 flex items-center gap-2">
        <!-- Icon -->
        <x-icon :name="$icon ?? 'exclamation-circle'" class="w-5 h-5" /> <!-- Default to 'exclamation-circle' if icon is not provided -->

        <!-- Title -->
        <span>{{ $title }}</span>

        <!-- Alert Count -->
        <span class="ml-auto text-sm font-normal bg-white px-2 py-1 rounded-full">
            {{ $alerts->count() }} {{ Str::plural('alert', $alerts->count()) }}
        </span>
    </h3>

    @forelse($alerts as $alert)
        <div class="py-2 border-b border-b-gray-200 last:border-b-0">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-medium">{{ $alert->product->name ?? 'Unknown Product' }}</p>
                    <p class="text-sm text-gray-600">{{ $alert->message }}</p>
                </div>

                <!-- Resolved Status -->
                @if($alert->resolved)
                    <span class="text-green-600 text-xs px-2 py-1 bg-white rounded-full flex items-center gap-1">
                        <x-icon name="check" class="w-3 h-3" />
                        Resolved
                    </span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-gray-500 italic">No {{ strtolower($title) }} at the moment.</p>
    @endforelse
</div>
