@php
$title = 'Commission Report';
$breadcrumbs = [
    ['Loans', '/heritage_financial/loans'],
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
    x-data="commission_reports()">

    <div class="max-w-full mx-auto sm:px-6 lg:px-12">

        <div class="flex flex-col">

            <div class="sm:-mx-6 lg:-mx-8"
            x-data="table({
                'container': $refs.container,
                'data_url': '/users/get_users',
                'active': true,
                'length': '10',
                'sort_by': 'last_name'
            })">

                <div class="flex justify-between items-center">

                    <div class="flex justify-start items-center">

                        <div class="p-2 ml-6 w-48">
                            <input
                            type="text"
                            class="form-element input md"
                            data-label="Search"
                            x-on:keyup="option_search($el.value)">
                        </div>

                    </div>


                </div>

                <div class="commission-reports-table py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8 overflow"
                x-ref="container"></div>

            </div>

        </div>

    </div>

    </div>

</x-app-layout>
