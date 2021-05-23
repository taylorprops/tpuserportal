
@php

if($size == 'sm') {
    $classes = 'px-3 py-1 text-xs ';
} else if($size == 'md') {
    $classes = 'px-4 py-2 text-sm ';
} else if($size == 'lg') {
    $classes = 'px-5 py-4 text-md ';
}



@endphp

<div class="flex items-start">

    <label class="flex justify-start bg-white w-full text-gray-600 rounded-lg border border-blue cursor-pointer" {!! $attributes -> merge(['class' => $classes]) !!}>
        <span class="{{ $classes }} mr-2 bg-default hover:bg-default-dark active:bg-default-dark focus:border-default-dark ring-default-dark inline-flex items-center border border-transparent rounded-l-md font-semibold text-white focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150"><i class="fal fa-upload mr-2"></i> Select A File</span>
        <div class="file-names my-auto truncate"></div>
        <input type="file" class="hidden" {!! $attributes -> merge() !!}  onchange="show_file_names(this)">
    </label>



</div>
