@props(['title', 'value', 'icon', 'color' => 'cyan', 'trend' => null])

<div class="glass-panel-dark p-4 text-center hover-scale">
    <div class="text-3xl mb-2 text-{{ $color }}-400 floating-icon">
        <i class="fas fa-{{ $icon }}"></i>
    </div>
    <div class="text-2xl font-bold text-white">{{ $value }}</div>
    <div class="text-sm text-gray-300">{{ __($title) }}</div>

    @if($trend)
        <div class="mt-2">
            @if($trend['direction'] === 'up')
                <div class="text-green-400 text-xs">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $trend['value'] }}%
                </div>
            @else
                <div class="text-red-400 text-xs">
                    <i class="fas fa-arrow-down mr-1"></i>{{ $trend['value'] }}%
                </div>
            @endif
        </div>
    @endif
</div>
