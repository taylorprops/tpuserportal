@php
$title = 'Schedule Settings';
$breadcrumbs = [
    ['Marketing', ''],
    ['Schedule', '/marketing/schedule'],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="schedule_settings()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12 pt-16">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                @php
                $fields = ['categories', 'mediums'];
                @endphp

                @foreach($fields as $field)

                    <div class="border rounded-md p-0">
                        <div class="bg-gray-50 text-lg p-4 rounded-t-md border-b-2">
                            {{ ucwords($field) }}
                        </div>
                        <div data-field="{{ $field }}"> </div>
                    </div>

                @endforeach

            </div>


        </div>


    </div>

</x-app-layout>
