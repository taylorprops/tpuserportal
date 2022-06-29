{{-- blade-formatter-disable --}}
@php
$title = 'Escrow';
$breadcrumbs = [
    [$title],
];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="escrow()">

        <div
        x-data="table({
            'container': $refs.container,
            'data_url': '/transactions_archived/get_escrow_html',
            'length': '10',
            'sort_by': 'contract_date',
            'button_export': true
        })">


            <div x-ref="container"></div>

        </div>

    </div>

</x-app-layout>

