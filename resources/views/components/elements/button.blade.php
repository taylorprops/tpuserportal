

@php
$classes = '';

if($size == 'sm') {
    $classes .= 'px-3 py-1 text-xs ';
} else if($size == 'md') {
    $classes .= 'px-4 py-2 text-sm ';
} else if($size == 'lg') {
    $classes .= 'px-5 py-4 text-md ';
}


$classes .= 'bg-'.$colorClass.' hover:bg-'.$colorClass.'-dark active:bg-'.$colorClass.'-dark focus:border-'.$colorClass.'-dark ring-'.$colorClass.'-dark inline-flex items-center border border-transparent rounded-md font-semibold text-white tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150 shadow-sm hover:shadow-md ';

@endphp

<button {{ $attributes -> merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
