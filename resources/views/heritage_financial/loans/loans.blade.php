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

            @if(auth() -> user() -> group != 'mortgage')
            <div class="mt-4 float-right">
                <button @click="window.location='/heritage_financial/loans/view_loan'" class="button primary lg"><i class="fal fa-plus mr-2"></i> Add Loan</button>
            </div>
            @endif

            <div
            x-data="table({
                'container': $refs.container,
                'data_url': '/heritage_financial/loans/get_loans',
                'length': '10',
                'sort_by': 'settlement_date'
            })">

                <div x-ref="container"></div>

            </div>

        </div>

    </div>

</x-app-layout>
