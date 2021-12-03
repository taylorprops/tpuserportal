<x-app-layout>
    @php
    $title = 'Loan Officers';
    $breadcrumbs = [
        ['Employees', ''],
        [$title]
    ];
    @endphp
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="employees()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8">

                    <div class="flex justify-between items-center">

                        <div class="flex justify-start items-center">

                            <div class="p-2 ml-6 w-48">
                                <input
                                type="text"
                                class="form-element input md"
                                    data-label="Search"
                                    x-on:keyup="init_table_search($el.value)">
                            </div>

                            <div class="p-2 ml-6 w-48">
                                <select
                                class="form-element select md"
                                id="table_show_active"
                                data-label="Active"
                                x-on:change="init_table_show_active($el.value)">
                                    <option value="">All</option>
                                    <option value="yes" selected>Active</option>
                                    <option value="no">Not Active</option>
                                </select>
                            </div>

                        </div>

                        <div class="mr-0 sm:mr-4 md:mr-10">
                            <a href="/employees/loan_officer/loan_officer_view" class="button primary lg"><i class="fal fa-plus mr-3"></i> Add Loan Officer</a>
                        </div>

                    </div>

                    {{-- <div class="w-screen-75 sm:w-screen-60 md:w-full overflow-x-auto"> --}}

                        <div class="employees-table py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8 overflow">
                        </div>

                    {{-- </div> --}}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
