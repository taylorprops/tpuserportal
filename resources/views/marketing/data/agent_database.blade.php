@php
$title = 'Agent Database';
$breadcrumbs = [
    ['Marketing', ''],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-48 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12"
        x-data="agent_database()">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">

                <div class="border p-4 rounded-lg">

                    <form id="search_form">

                        <div class="mb-4 text-xl font-semibold">
                            Enter The List Criteria
                        </div>

                        <div class="my-6">

                            <div class="text-base font-medium text-gray-900 mb-2">List Type</div>
                            <p class="text-sm leading-5 text-gray-500">Is this for an Email or Address list?</p>

                            <fieldset class="mt-4">

                                <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_type"
                                        value="email"
                                        data-label="Emails"
                                        checked>
                                    </div>

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_type"
                                        value="address"
                                        data-label="Home Addresses">
                                    </div>

                                </div>

                            </fieldset>

                        </div>

                        <div class="text-base font-medium text-gray-900 mb-2 mt-12">Location</div>

                        <div class="flex">

                            <div class="p-2">

                                <div class="text-gray-500 mb-3">States</div>

                                <div class="p-2 rounded bg-gray-50 w-64 max-h-300-px overflow-y-auto">

                                    @foreach($states as $state)

                                        <div class="">

                                            <input type="checkbox"
                                            class="form-element checkbox xl primary"
                                            name="states[]" value="{{ $state }}"
                                            data-label="{{ $state }}"
                                            @if($state == 'MD') checked @endif
                                            @click="location_data('{{ $state }}', true, false);">

                                        </div>

                                    @endforeach

                                </div>

                                <div class="mt-2 flex justify-between">
                                    <div class="ml-2.5">
                                        <input type="checkbox"
                                        class="form-element checkbox lg primary"
                                        data-label="Select All"
                                        @click="select_all_options('states', $el.checked);">
                                    </div>
                                    <span class="bg-yellow-600 rounded-full py-1 px-3 text-xs text-white inline-block"><span id="state_count"></span> Selected</span>
                                </div>

                            </div>

                            <div class="ml-8 p-2">

                                <div class="text-gray-500 mb-3">Counties</div>


                                <div class="p-2 rounded bg-gray-50 w-96 max-h-300-px overflow-y-auto">

                                    <template
                                    x-for="county in counties">
                                        <div class="county-checkbox">
                                            <input type="checkbox"
                                            class="form-element checkbox xl primary"
                                            name="counties[]"
                                            :data-state="county.state"
                                            :data-label="county.state+' - '+county.county"
                                            :value="county.state+'-'+county.county"
                                            @click="search_offices(); update_details(); get_checked(false)">
                                        </div>
                                    </template>

                                </div>

                                <div class="mt-2 flex justify-between">
                                    <div class="ml-2.5">
                                        <input type="checkbox"
                                        x-ref="select_all_counties"
                                        class="form-element checkbox lg primary"
                                        data-label="Select All"
                                        @click="select_all_options('counties', $el.checked);">
                                    </div>
                                    <span class="bg-yellow-600 rounded-full py-1 px-3 text-xs text-white inline-block"><span id="county_count"></span> Selected</span>
                                </div>

                            </div>

                        </div>

                        <div class="text-base font-medium text-gray-900 mb-2 mt-12">Offices</div>

                        <div class="max-w-sm">
                            <input type="text" id="office_search" class="form-element input md" placeholder="Search..." data-label="Office Name"
                            @input.debounce="search_offices()">
                        </div>

                        <div id="office_search_results"></div>



                    </form>

                </div>


                <div class="border p-4 rounded-lg">

                    <div class="mb-4 text-xl font-semibold">
                        Results
                    </div>

                </div>


        </div>

    </div>

</x-app-layout>
