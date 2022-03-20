@php
$title = 'System Monitor';
$breadcrumbs = [
    ['Super Admin', ''],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">

                <div class="border rounded-md shadow">

                    <div class="bg-gray-50 p-4 border-b text-lg rounded-t-md">
                        Database Backups
                    </div>

                    <div class="p-4">

                    </div>

                </div>


            </div>

        </div>

    </div>

</x-app-layout>
