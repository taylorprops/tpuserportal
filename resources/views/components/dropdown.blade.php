@props([
    'align' => 'right',
    'width' => 'w-72',
    'contentClasses' => 'p-4 bg-white',
    'buttonText',
    'class',
    'size'
])

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

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">

    <x-elements.button
        @click="$dispatch('show-modal-{{ $dispatchId }}')"
        :colorClass="'default'"
        :size="'md'"
        type="button">
        <i class="fal fa-plus mr-2"></i> Add Form
    </x-elements.button>

    <x-elements.button type="button" :class="$class" :size="$size" @click="open = ! open">
        {{ $buttonText }}
    </x-elements.button>


    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $slot }}
        </div>
    </div>
</div>
