
@php

switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}


@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">


    <x-elements.button type="button" :buttonClass="$buttonClass" :buttonSize="$buttonSize" @click="open = ! open">
        {{ $buttonText }}
    </x-elements.button>


    <div x-show="open"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        class="absolute z-50 mt-2 {{ $dropdownWidth }} rounded-md shadow-lg {{ $alignmentClasses }} ring-1 ring-black ring-opacity-5 {{ $dropdownClasses }}"
        @click.outside="open = false">
            {{ $slot }}
    </div>
</div>
