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
        multiple
        id="{{ $id }}"
        placeholder="{{ $attributes['placeholder'] }}"
        {!! $attributes -> merge(['class' => $classes]) !!}>
        {{ $slot }}
    </select>
    <div class="relative">
            <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
        </div>
</label>

