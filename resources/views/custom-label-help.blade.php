@props([
    'tooltip' => '',
    'icon' => 'heroicon-o-question-mark-circle',
    'align' => '',
    'size' => '',
    'position' => ''
])
<div class="tooltip w-4 h-4 mx-2 align-text-top cursor-pointer">{{ svg($icon) }}
    <span 
        class="
            tooltiptext 
            {{ $align ? 'align-' . $align : '' }} 
            {{ $size ? 'size-' . $size : '' }} 
            {{ $position ? 'position-' . $position : '' }}" 
    >
        {!! $tooltip !!}
    </span>
</div>