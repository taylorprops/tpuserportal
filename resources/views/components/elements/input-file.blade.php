{{-- blade-formatter-disable --}}
@php

if ($size == 'sm') {
    $classes = 'px-3 py-1 text-xs';
} elseif ($size == 'md') {
    $classes = 'px-3 pt-2 pb-1 text-sm';
} elseif ($size == 'lg') {
    $classes = 'px-3 py-3 text-base';
} elseif ($size == 'xl') {
    $classes = 'px-3 py-3 text-lg';
}

$required = $attributes['class'] == 'required' ? true : false;
@endphp
{{-- blade-formatter-enable --}}

<div class="flex items-start">

    <label class="whitespace-nowrap bg-white w-full {{ $classes }}">

        <div class="file-input-div flex justify-start rounded-sm border border-gray-300 cursor-pointer {{ $required ? 'required' : '' }}">

            <span
                class="{{ $classes }} mr-2 bg-{{ $buttonClass }} hover:bg-{{ $buttonClass }}-dark active:bg-{{ $buttonClass }}-dark focus:border-{{ $buttonClass }}-dark ring-{{ $buttonClass }}-dark inline-flex items-center border border-transparent rounded-sm-l font-semibold text-white focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150"><i class="fal fa-upload mr-2"></i> Select A File</span>

            <div class="file-names my-auto truncate"></div>

            <input type="file" {{ $attributes -> merge(['class' => 'hidden']) }} x-on:change="remove_form_errors($event); show_file_names($event); ">

        </div>

        <div class="relative">
            <span class="text-red-500 text-xs error-message h-4 inline-block absolute top-0"></span>
        </div>
    </label>
</div>
