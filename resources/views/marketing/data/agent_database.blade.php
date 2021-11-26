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

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12"
        x-data="agent_database()">

            <div class="">

                <form id="search_form">

                    <div class="mt-12 mb-4 text-xl font-semibold">
                        Enter The List Criteria
                    </div>

                    <div class="my-6">

                        <div class="text-base font-medium text-gray-900 mb-2">List Type</div>
                        <p class="text-sm leading-5 text-gray-500">Is this for an Email or Address list?</p>

                        <fieldset class="mt-4">

                            <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">

                                <div class="flex items-center">
                                    <input id="type_email" name="list_type" type="radio" value="email" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="type_email" class="ml-3 block text-sm font-medium text-gray-700">
                                        Emails
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input id="type_address" name="list_type" type="radio" value="address" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="type_address" class="ml-3 block text-sm font-medium text-gray-700">
                                        Home Addresses
                                    </label>
                                </div>

                            </div>

                        </fieldset>

                    </div>

                    <div class="text-base font-medium text-gray-900 mb-2 mt-12">Location</div>

                    <div class="grid grid-cols-4 gap-4">

                        <div>
                            <select
                            class="form-element select md h-48"
                            id="states"
                            name="states[]"
                            data-label="State"
                            multiple
                            @change="document.querySelector('#counties').value = ''; location_data();">
                                @foreach($states as $state)
                                    <option value="{{ $state -> OfficeStateOrProvince }}" @if($state -> OfficeStateOrProvince == 'MD') selected @endif>{{ $state -> OfficeStateOrProvince }}</option>
                                @endforeach
                            </select>
                            <div class="mt-2 flex justify-around">
                                <span class="bg-yellow-600 rounded-full p-2 text-sm text-white inline-block"><span id="state_count"></span> Selected</span>
                            </div>
                        </div>

                        <div>
                            <select
                            class="form-element select md h-48"
                            id="counties"
                            name="counties[]"
                            data-label="Counties"
                            multiple
                            x-ref="counties_select"
                            @change="location_data()">
                                <option value=""></option>
                                <template
                                x-for="county in counties">
                                    <option :value="county.county" x-text="county.state+' - '+county.county"></option>
                                </template>
                            </select>
                            <div class="mt-2 flex justify-around">
                                <span class="bg-yellow-600 rounded-full p-2 text-sm text-white inline-block"><span id="county_count"></span> Selected</span>
                                <button type="button" class="button primary md"
                                @click="select_all_options($refs.counties_select); $nextTick(() => { location_data() });">
                                    <i class="fal fa-check mr-2"></i> Select All
                                </button>
                            </div>
                        </div>

                        {{-- <div>
                            <select
                            class="form-element select md h-48"
                            id="cities"
                            name="cities[]"
                            data-label="Cities"
                            multiple
                            x-ref="cities_select"
                            @change="$refs.city_count.innerText = Array.from($el.selectedOptions).length">
                                <option value=""></option>
                                <template
                                x-for="city in cities">
                                    <option :value="city.city" x-text="city.city+' - '+city.state"></option>
                                </template>
                            </select>
                            <div class="mt-2 flex justify-around">
                                <span class="bg-yellow-600 rounded-full p-2 text-sm text-white inline-block"><span x-ref="city_count" id="city_count"></span> Selected</span>
                                <button type="button" class="button primary md"
                                @click="select_all_options($refs.cities_select)">
                                    <i class="fal fa-check mr-2"></i> Select All
                                </button>
                            </div>
                        </div> --}}

                    </div>

                    <div class="text-base font-medium text-gray-900 mb-2 mt-12">Offices</div>

                    <div class="flex justify-start items-start">

                        <div class="mr-4">
                            <input type="text" class="form-element input md" placeholder="Search..." data-label="Office Name"
                            @input.debounce="search_offices($el.value)">
                        </div>

                        <div>
                            <div id="office_search_results" class="max-h-96 overflow-y-auto"></div>
                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
