@php
$slider_id = time() * rand();
$id = $attributes['id'];
$name = $attributes['name'];
$value = $attributes['value'];
$min = $attributes['min'];
$max = $attributes['max'];
$step = $attributes['step'];
@endphp
<div x-data="{ slider_{{ $id }}: {{ $value }} }">
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" x-model="slider_{{ $id }}" />
    <div class="flex">
        <div class="flex-none w-12 text-center">{{ $min }}</div>
        <div class="flex-grow">
            <div class="bg-default-light p-0 rounded-xl h-1 relative block">
                <div class="range-wrap">
                    <input class="range w-full bg-gray-500 absolute -bottom-2 h-3 cursor-pointer" type="range" x-model="slider_{{ $id }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}">
                    <output class="bubble bg-default-light text-white h-5 w-8 p-0.5 text-xs text-center rounded-sm top-4"></output>
                </div>
            </div>
        </div>
        <div class="flex-none w-12 text-center">{{ $max }}</div>
    </div>
</div>
