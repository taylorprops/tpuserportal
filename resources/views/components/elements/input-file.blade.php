
@php

if($size == 'sm') {
    $classes = 'px-3 py-1 text-xs';
} else if($size == 'md') {
    $classes = 'px-3 pt-2 pb-1 text-sm';
} else if($size == 'lg') {
    $classes = 'px-3 py-3 text-base';
} else if($size == 'xl') {
    $classes = 'px-3 py-3 text-lg';
}

$required = $attributes['class'] == 'required' ? true : false;
@endphp

<div class="flex items-start">

    <label class="whitespace-nowrap bg-white w-full {{ $classes }}">

        <div class="file-input-div flex justify-start text-gray-600 rounded-sm border border-gray-300 cursor-pointer {{ $required ? 'required' : '' }}">

            <span class="{{ $classes }} mr-2 bg-{{ $buttonClass }} hover:bg-{{ $buttonClass }}-dark active:bg-{{ $buttonClass }}-dark focus:border-{{ $buttonClass }}-dark ring-{{ $buttonClass }}-dark inline-flex items-center border border-transparent rounded-sm-l font-semibold text-white focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150"><i class="fal fa-upload mr-2"></i> Select A File</span>

            <div class="file-names my-auto truncate"></div>

            <input type="file" {{ $attributes -> merge(['class' => 'hidden']) }} x-on:change="remove_form_errors($event); show_file_names($event); ">

        </div>

        <div class="relative">
            <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
        </div>
    </label>



</div>
