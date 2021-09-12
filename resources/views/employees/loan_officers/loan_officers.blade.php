<x-app-layout>
    @section('title') Loan Officers @endsection
    <x-slot name="header">
        Loan Officers
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="loan_officers()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8">

                    <div class="flex justify-start items-center">

                        <div class="p-2 ml-6 w-48">
                            <x-elements.input
                                id="search"
                                data-label="Search"
                                placeholder=""
                                :size="'md'"
                                x-on:keyup="search($el.value)"/>
                        </div>

                        <div class="p-2 ml-6 w-48">
                            <x-elements.select
                            id="active"
                            data-label="Active"
                            :size="'md'"
                            x-on:change="show_active($el.value)">
                                <option value="">All</option>
                                <option value="yes" selected>Active</option>
                                <option value="no">Not Active</option>
                            </x-elements.select>
                        </div>

                    </div>

                    {{-- <div class="w-screen-75 sm:w-screen-60 md:w-full overflow-x-auto"> --}}

                        <div class="loan-officers-table py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8 overflow">
                        </div>

                    {{-- </div> --}}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
