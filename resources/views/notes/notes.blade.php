@php
$title = 'Notes';
$breadcrumbs = [[$title]];
@endphp
<x-app-layout>
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2"
        x-data="notes()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">



            <div class="mt-24">

                <div id="notes" x-ref="notes" class="w-full lg:w-1000-px mx-auto border-2 rounded-lg p-4">{!! $notes -> notes !!}</div>

                <div class="flex justify-around mt-8">
                    <button type="button" class="button primary lg" @click="save_notes($el)">Save Notes <i class="fa-light fa-check ml-2"></i></button>
                </div>

            </div>

        </div>

    </div>

</x-app-layout>
