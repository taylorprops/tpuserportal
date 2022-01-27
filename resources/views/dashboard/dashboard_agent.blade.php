@php
$title = 'Dashboard';
$breadcrumbs = [];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="">

                Agent Dashboard

            </div>

        </div>

    </div>

</x-app-layout>
