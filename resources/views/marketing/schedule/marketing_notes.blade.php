{{-- blade-formatter-disable --}}
@php
$title = 'Marketing Notes';
$breadcrumbs = [
    ['Schedule', '/marketing/schedule'],
    [$title],
];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
        x-data="notes('schedule')">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="mt-24">
                <div id="notes" x-ref="notes"></div>

            </div>

        </div>

    </div>

</x-app-layout>
