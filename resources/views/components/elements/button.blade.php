{{-- blade-formatter-disable --}}
@php

$classes = '';

if($buttonSize == 'sm') {
    $classes .= 'px-3 py-1 text-xs';
} else if($buttonSize == 'md') {
    $classes .= 'px-3 py-2 text-sm';
} else if($buttonSize == 'lg') {
    $classes .= 'px-3 py-3 text-base ';
} else if($buttonSize == 'xl') {
    $classes .= 'px-3 py-3 text-lg';
}

$classes .= ' bg-'.$buttonClass.' hover:bg-'.$buttonClass.'-dark active:bg-'.$buttonClass.'-dark focus:border-'.$buttonClass.'-dark ring-'.$buttonClass.'-dark inline-flex items-center rounded tracking-wider text-white shadow-md hover:shadow-lg outline-none focus:outline-none disabled:opacity-25 transition-all ease-in-out duration-150 shadow hover:shadow-md ';
@endphp
{{-- blade-formatter-enable --}}
<button {{ $attributes -> merge(['class' => $classes]) }} data-default-html="{{ htmlspecialchars($slot) }}">
    {{ $slot }}
</button>
