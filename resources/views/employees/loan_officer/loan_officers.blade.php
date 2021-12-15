<x-app-layout>
    @php
    $title = 'Mortgage';
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
                    'data_url': '/employees/loan_officer/get_loan_officers',
                    'active': 'yes',
                    'length': '10',
                    'sort_by': 'last_name',
                    'buttons': [
                        {
                            'html': '<i class=\'fal fa-plus mr-2\'></i> Add Loan Officer',
                            'url': '/employees/loan_officer/loan_officer_view'
                        }
                    ]
                })">

                    <div x-ref="container"></div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
