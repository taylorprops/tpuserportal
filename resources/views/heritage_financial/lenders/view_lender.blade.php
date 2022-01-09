@php
$title = $lender -> copmany_name ?? 'Add Lender';
$breadcrumbs = [
    ['Heritage Financial', '/heritage_financial/lenders'],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="">



            </div>

        </div>

    </div>

</x-app-layout>
