@php
$title = 'Loans';
$breadcrumbs = [
    ['Heritage Financial', ''],
    ['Loans', ''],
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

            <div class="mt-4">
                <button @click="window.location='/heritage_financial/view_loan'" class="button primary lg"><i class="fal fa-plus mr-2"></i> Add Loan</button>
            </div>

        </div>

    </div>

</x-app-layout>
