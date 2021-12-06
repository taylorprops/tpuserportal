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

    <div class="pb-12 pt-2"
    x-data="dashboard('{{ $group }}')">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12 pt-4 md:pt-8 lg:pt-16">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 max-w-1200-px gap-12 mx-auto">

                <div class="border-4 rounded-lg">

                    <div class="rounded-t-lg border-b p-3 text-lg font-semibold">
                        Active Loans
                    </div>

                    <div class="p-2">

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

