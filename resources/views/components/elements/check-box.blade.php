@php

$classes = 'text-'.$color.'-600 ';

if($size == 'sm') {
    $classes .= 'h-3 w-3';
    $text_size = 'text-sm';
} else if($size == 'md') {
    $classes .= 'h-4 w-4';
    $text_size = 'text-base';
} else if($size == 'lg') {
    $classes .= 'h-5 w-5';
    $text_size = 'text-lg';
} else if($size == 'xl') {
    $classes .= 'h-6 w-6';
    $text_size = 'text-xl';
}

@endphp

<label class="inline-flex items-center {{ $text_size }}">
    <input type="checkbox" class="form-checkbox {{ $classes }}" {!! $attributes -> merge(['class' => $classes]) !!}><span class="ml-2">{{ $label }}</span>
    <div class="relative">
        <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
    </div>
</label>
