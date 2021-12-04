<x-app-layout>
    @php
    $title = 'In House Employees';
    $breadcrumbs = [
        ['Employees', ''],
        [$title]
    ];
    @endphp
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="employees()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8"
                x-data="table({
                    'container': $refs.container,
                    'data_url': '/employees/in_house/get_in_house',
                    'active': true,
                    'length': '10',
                    'sort_by': 'last_name',
                    'button': {
                        'html': '<i class=\'fal fa-plus mr-2\'></i> Add In House Employee',
                        'url': '/employees/in_house/in_house_view'
                    }
                })">

                    <div class="table-container"  x-ref="container"></div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

