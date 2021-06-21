
@php

if($size == 'sm') {
    $classes = 'px-3 py-1 text-xs';
    $label_text_size = 'text-xs';
} else if($size == 'md') {
    $classes = 'px-3 pt-2 pb-1 text-sm';
    $label_text_size = 'text-sm';
} else if($size == 'lg') {
    $classes = 'px-3 py-3 text-base';
    $label_text_size = 'text-sm';
} else if($size == 'xl') {
    $classes = 'px-3 py-3 text-lg';
    $label_text_size = 'text-lg';
}

$classes .= ' w-full rounded-sm border-gray-300 focus:outline-none focus:ring-blue-300 focus:border-blue-200 focus:shadow-sm disabled:opacity-50';
$label = $attributes['data-label'] ?? null;

$id = time() * rand();
if($attributes['id']) {
    $id = $attributes['id'];
}
@endphp

<label class="text-gray-500 italic block {{ $label_text_size }}">
    <span class="ml-2">{{ $label }}</span>
    <select
        id="{{ $id }}"
        placeholder="{{ $attributes['placeholder'] }}"
        {!! $attributes -> merge(['class' => $classes]) !!}>
        {{ $slot }}
    </select>
    <div class="relative">
            <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
        </div>
</label>

{{-- <label class="text-gray-500 italic block {{ $label_text_size }}"
x-data="{ show_select_dropdown: false }">

    <span class="ml-2">{{ $label }}</span>

    <input type="text" class="p-1 rounded-sm select-search"
    id="{{ $id }}"
    placeholder="{{ $attributes['placeholder'] }}"
    {!! $attributes -> merge(['class' => $classes]) !!}
    @keydown="show_select_dropdown = true"/>

    <div class="relative">

        <div class="absolute w-auto h-48 overflow-auto bg-white z-30 border shadow"
        x-show="show_select_dropdown">
            <div class="p-2 border-b">
                <input type="text" class="p-1 rounded-sm select-search" placeholder="Search">
            </div>
            <ul>
            {{ $slot }}
            </ul>
        </div>

    </div>

    <div class="relative">
        <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
    </div>

</label> --}}

{{-- <label class="text-gray-500 italic block {{ $label_text_size }}">
    <x-elements.input
    id="{{ $id }}"
    name="{{ $id }}"
    placeholder="{{ $attributes['placeholder'] }}"
    data-label="{{ $attributes['data-label'] }}"
    :size="'md'"
    required list="{{ $id }}-list"/>
    <datalist id="{{ $id }}-list">
    {{ $slot }}
    </datalist>
</label> --}}
