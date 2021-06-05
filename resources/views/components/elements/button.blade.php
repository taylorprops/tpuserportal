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

$classes .= ' bg-'.$buttonClass.' hover:bg-'.$buttonClass.'-dark active:bg-'.$buttonClass.'-dark focus:border-'.$buttonClass.'-dark ring-'.$buttonClass.'-dark inline-flex items-center border border-'.$buttonClass.'-dark rounded font-semibold text-white tracking-tight focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150 shadow hover:shadow-md ';
@endphp
<button {{ $attributes -> merge(['class' => $classes]) }} data-default-html="{{ htmlspecialchars($slot) }}">
    {{ $slot }}
</button>
