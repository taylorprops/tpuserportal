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

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8">

                    <div class="p-2 ml-6 w-80">
                        <input
                        type="text"
                        class="form-element input lg"
                        placeholder="Search"
                        x-on:keyup="init_table_search($el.value)"/>
                    </div>

                    {{-- <div class="w-screen-75 sm:w-screen-60 md:w-full overflow-x-auto"> --}}

                        <div class="archives-table py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        </div>

                    {{-- </div> --}}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
