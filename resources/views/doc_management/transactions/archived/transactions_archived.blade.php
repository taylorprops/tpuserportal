@php
$title = 'Transactions Archived';
$breadcrumbs = [
    ['Transactions', '/transactions/transactions'],
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
    x-data="archives()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8"
                x-data="table({
                    'container': $refs.container,
                    'data_url': '/get_transactions_archived',
                    'length': '10',
                    'sort_by': 'actualClosingDate'
                })">

                    <div x-ref="container"></div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
