@php
$title = 'Escrow';
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

    <div class="pb-12 pt-2"
    x-data="escrow()">

        <div class="p-2 mb-3 sm:mt-1 md:mt-2 lg:mt-3 ml-0 xl:ml-6 w-48 lg:w-80">
            <input
            type="text"
            class="form-element input md"
            placeholder="Search"
            x-on:keyup="init_table_search($el.value)"/>
        </div>

        <div class="w-screen-95 mx-auto sm:w-full overflow-x-auto">

            <div class="escrow-table min-w-1000 text-xs md:text-sm px-0 sm:px-3 lg:px-8 whitespace-nowrap"></div>

        </div>

    </div>

</x-app-layout>

