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
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="commission_reports()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="text-xl text-gray-700 my-4">
                Select the dates and then click the "To Excel" button to generate a report
            </div>

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8" x-data="table({
                'container': $refs.container,
                'data_url': '/heritage_financial/loans/get_commission_reports',
                'length': '10',
                'sort_by': 'settlement_date',
                'button_export': true,
                'search': false,
                'dates': {
                    'col': 'settlement_date',
                    'text': 'Settlement Date'
                },
                'additional_html': '<div class=\'text-xl text-gray-700\'>Results</div>',
            })">

                    <div x-ref="container"></div>

                </div>

            </div>

        </div>

</x-app-layout>
