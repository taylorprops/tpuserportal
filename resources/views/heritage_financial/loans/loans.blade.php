@php
$title = 'Loans';
$breadcrumbs = [
    ['Heritage Financial', ''],
    [$title, ''],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="loans()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            @if(auth() -> user() -> level != 'loan_officer')
            <div class="mt-4 float-right">
                <button @click="window.location='/heritage_financial/loans/view_loan'" class="button primary lg"><i class="fal fa-plus mr-2"></i> Add Loan</button>
            </div>
            @endif

            <div
            x-data="table({
                'container': $refs.container,
                'data_url': '/heritage_financial/loans/get_loans',
                'length': '10',
                'sort_by': 'settlement_date',
                'fields': {
                    '1': {
                        type: 'select',
                        db_field: 'processor_id',
                        label: 'Processor',
                        @if(in_array(auth() -> user() -> level, ['manager', 'processor']))
                        value: '{{ auth() -> user() -> user_id }}',
                        @endif
                        options: [
                            ['', 'All'],
                            @foreach($processors as $processor)
                                ['{{ $processor -> id }}', '{{ $processor -> fullname }}'],
                            @endforeach
                        ]
                    },
                    '2': {
                        type: 'select',
                        db_field: 'loan_status',
                        label: 'Status',
                        value: 'Open',
                        options: [
                            ['all', 'All'],
                            ['Open', 'Open'],
                            ['Closed', 'Closed'],
                            ['Cancelled', 'Cancelled'],
                        ]
                    }
                }
            })">

                <div x-ref="container"></div>

            </div>

        </div>

    </div>

</x-app-layout>
