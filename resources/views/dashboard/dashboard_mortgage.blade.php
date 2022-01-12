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

    <div class="pb-24 lg:pb-36 pt-2"
    x-data="dashboard('{{ $group }}')">

        <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-12 pt-4 md:pt-8">

            <div class="max-w-1400-px mx-auto">


                <div class="border-4 rounded-lg max-w-1400-px">
                    @include('dashboard/includes/mortgage/active_loans')
                </div>

                <div class="border-4 rounded-lg mt-12 max-w-900-px">

                    @include('dashboard/includes/mortgage/recently_closed_loans')

                </div>


                <div class="border-4 rounded-lg mt-12">

                    @include('dashboard/includes/mortgage/software_marketing')

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

