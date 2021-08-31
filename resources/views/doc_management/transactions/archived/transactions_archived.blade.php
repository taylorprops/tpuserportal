<x-app-layout>
    @section('title') Archives @endsection
    <x-slot name="header">
        Archives
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="archives()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                    <div class="p-2 ml-6 w-80">
                        <x-elements.input
                            id="search"
                            name=""
                            data-label=""
                            placeholder="Search"
                            :size="'lg'"
                            x-on:keyup="search($el.value)"/>
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
