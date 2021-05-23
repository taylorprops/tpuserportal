@props([
    'disabled' => false
    ])

@php

if($size == 'sm') {
    $classes = 'px-3 py-1 text-sm ';
    $label_top = '';
} else if($size == 'md') {
    $classes = 'px-4 pt-2 pb-1';
    $label_top = 'top-2';
} else if($size == 'lg') {
    $classes = 'px-5 py-4 text-md ';
    $label_top = '';
}

$classes .= ' rounded border-gray-300 focus:outline-none focus:ring-blue-300 focus:border-blue-200 focus:shadow-sm w-full';
$label = $attributes['data-label'];

$id = time();
if($attributes['id']) {
    $id = $attributes['id'];
}
@endphp

<div class="floating-input mb-5 relative">
    <input id="{{ $id }}"
    placeholder="{{ $attributes['placeholder'] }}"
    autocomplete="off"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes -> merge(['class' => $classes]) !!} />
    <label for="email" class="absolute {{ $label_top }} rounded-lg text-gray-500 left-0 mx-3 px-2 py-0 pointer-events-none transform origin-left transition-all duration-100 ease-in-out">{{ $label }}</label>
</div>
