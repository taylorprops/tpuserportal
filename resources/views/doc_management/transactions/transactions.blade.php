@php
$title = 'Transactions';
$breadcrumbs = [
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



        </div>

    </div>

</x-app-layout>
