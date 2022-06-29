<x-app-layout>
    {{-- blade-formatter-disable --}}
    @php
        $title = 'Mortgage';
        $breadcrumbs = [['Employees', ''], [$title]];
    @endphp
{{-- blade-formatter-enable --}}
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="email_list();">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8" x-data="table({
                    'container': $refs.container,
                    'data_url': '/employees/loan_officer/get_loan_officers',
                    'length': '10',
                    'sort_by': 'last_name',
                    'buttons': [{
                        'html': '<i class=\'fal fa-plus mr-2\'></i> Add Loan Officer',
                        'url': '/employees/loan_officer/loan_officer_view'
                    }],
                    'fields': {
                        '1': {
                            type: 'select',
                            db_field: 'active',
                            label: 'Active',
                            value: 'yes',
                            options: [
                                ['all', 'All'],
                                ['yes', 'Yes'],
                                ['no', 'No'],
                            ]
                        }
                    }
                })">

                    <div x-ref="container"></div>

                </div>

            </div>

        </div>

        @include('components/modals/email_list_modal', [
            'company' => 'Heritage Financial',
            'title' => 'Email Loan Officers',
        ])

    </div>

</x-app-layout>
