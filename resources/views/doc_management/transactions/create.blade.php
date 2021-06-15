<x-app-layout>
    @section('title') Add {{ $details['header'] }} @endsection
    <x-slot name="header">
        <i class="{{ $details['icon'] }} mr-3"></i> Add {{ $details['header'] }}
    </x-slot>

    <div class="create-div pb-12 pt-2"
    x-data="create('{{ $transaction_type }}')"
    x-init="address_search(), transaction_type = '{{ $transaction_type }}'">

        <div class="max-w-screen-lg mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden md:mt-4 lg:mt-12 shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- STEPS --}}
                    <nav aria-label="Progress">

                        <ol class="space-y-4 md:flex md:space-y-0 md:space-x-8">

                            <li class="md:flex-1">
                                <a href="javascript:void(0)"
                                class="group pl-4 py-2 flex flex-col border-l-4  md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4 border-secondary hover:border-secondary-dark"
                                @click="active_step = '1'">
                                    <span class="text-xs text-secondary font-semibold tracking-wide uppercase group-hover:text-secondary-dark">Step 1</span>
                                    <span class="text-sm font-medium mt-2">Locate Property</span>
                                </a>
                            </li>

                            <li class="md:flex-1 @if($transaction_type == 'referral') hidden @endif">
                                <a href="javascript:void(0)"
                                class="pl-4 py-2 flex flex-col border-l-4 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4" aria-current="step"
                                :class="{ 'border-secondary hover:border-secondary-dark': active_step >= 2, 'border-gray-200 hover:border-gray-300': active_step < 2 }"
                                @click="active_step = '2'">
                                    <span class="text-xs font-semibold tracking-wide uppercase"
                                    :class="{ 'text-secondary hover:text-secondary-dark': active_step >= 2, 'text-gray-500 hover:text-gray-700': active_step < 2 }">Step 2</span>
                                    <span class="text-sm font-medium mt-2">Enter Checklist Details</span>
                                </a>
                            </li>

                            <li class="md:flex-1">
                                <a href="javascript:void(0)"
                                class="group pl-4 py-2 flex flex-col border-l-4 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4"
                                :class="{ 'border-secondary hover:border-secondary-dark': active_step === '3', 'border-gray-200 hover:border-gray-300': active_step !== '3' }"
                                @click="active_step = '3'">
                                    <span class="text-xs font-semibold tracking-wide uppercase group-hover:text-gray-700"
                                    :class="{ 'text-secondary hover:text-secondary-dark': active_step === '3', 'text-gray-500 hover:text-gray-700': active_step !== '3' }">Step @if($transaction_type == 'referral') 2 @else 3 @endif</span>
                                    <span class="text-sm font-medium mt-2">Enter Required Details</span>
                                </a>
                            </li>

                        </ol>

                    </nav>

                    <hr class="text-primary my-5">


                    <div class="py-6 md:py-8">

                        {{-- STEP 1 --}}
                        <div x-show.transition="active_step === '1'">

                            {{-- Nav - Mobile --}}
                            <div class="sm:hidden">

                                <x-elements.select id="my_select" name=""
                                data-label="Locate Property By"
                                :size="'md'"
                                x-on:change="search_type = $event.target.value; clear_results_and_errors();">
                                    <option value="address" selected>Search Street Address </option>
                                    <option value="mls">Search MLS ID</option>
                                    <option value="manually">Enter Manually</option>
                                </x-elements.select>

                            </div>

                            {{-- Nav - Desktop --}}
                            <div class="hidden sm:block">

                                <div class="border-b border-gray-200">

                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                                        <a href="javascript:void(0)"
                                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                        @click.prevent="search_type = 'address'; clear_results_and_errors()"
                                        :class="{'border-secondary text-secondary': search_type === 'address', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': search_type !== 'address'}">
                                            Search Street Address
                                        </a>

                                        <a href="javascript:void(0)"
                                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                        @click.prevent="search_type = 'mls'; clear_results_and_errors()"
                                        :class="{'border-secondary text-secondary': search_type === 'mls', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': search_type !== 'mls'}">
                                            Search MLS ID
                                        </a>

                                        <a href="javascript:void(0)"
                                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                        @click.prevent="search_type = 'manually'; clear_results_and_errors()"
                                        :class="{'border-secondary text-secondary': search_type === 'manually', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': search_type !== 'manually'}">
                                            Enter Manually
                                        </a>

                                    </nav>

                                </div>

                            </div>

                            {{-- Address/MLS Options --}}
                            <div class="py-6 md:py-24 bg-white">

                                {{-- Address Search Div --}}
                                <div class="w-2/3 mx-auto"
                                x-show.transition="search_type === 'address'">

                                    <div class="grid grid-cols-7">

                                        <div class="col-span-6">
                                            <x-elements.input
                                            id="address_search_input"
                                            placeholder=""
                                            data-label="Enter the Street Address"
                                            :size="'xl'"
                                            @keydown="show_street_error = false"/>
                                        </div>
                                        <div class="col-span-1 ml-2">
                                            <x-elements.input
                                            id="address_search_unit"
                                            placeholder=""
                                            data-label="Unit"
                                            :size="'xl'"/>
                                        </div>

                                    </div>

                                    <div class="mt-4">
                                        <x-elements.button
                                            class="address-search"
                                            :buttonClass="'primary'"
                                            :buttonSize="'lg'"
                                            type="button"
                                            @click="get_property_info($event.currentTarget, search_type)">
                                            Continue <i class="fal fa-arrow-right ml-2"></i>
                                        </x-elements.button>
                                    </div>


                                    {{-- Errors --}}

                                    <div class="mt-8"
                                    x-show.transition="show_no_property_error">
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                            <p class="font-bold">Error</p>
                                            <p>
                                                Property not found. Please retry or enter the address manually.
                                                <div class="w-full text-center mt-3">
                                                    <button class="px-2 py-1 rounded-xl border-2 border-white text-center text-pink-700 hover:bg-red-200"
                                                    @click="search_type = 'manually'; clear_results_and_errors()">
                                                        Enter Manually
                                                    </button>
                                                </div>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-8"
                                    x-show.transition="show_street_error">
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                            <p class="font-bold">Error</p>
                                            <p>
                                                Street Number not valid. Please retry or enter the address manually.
                                                <div class="w-full text-center nt-3">
                                                    <button class="px-2 py-1 rounded-xl border-2 border-white text-center text-pink-700 hover:bg-red-200"
                                                    @click="search_type = 'manually'; clear_results_and_errors()">
                                                        Enter Manually
                                                    </button>
                                                </div>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-8"
                                    x-show.transition="show_license_state_error">
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                            <p class="font-bold">Error</p>
                                            <p>You can only add properties from states the company is licesned in.</p>
                                        </div>
                                    </div>

                                    <div class="mt-8"
                                    x-show.transition="show_multiple_error">
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                            <p class="font-bold">Error</p>
                                            <p>
                                                Mutliple results were returned for that address. Please select from the list below, enter the unit or enter the address manually.
                                                <button class="px-2 py-1 ml-3 rounded-xl border-2 border-white text-center text-pink-700 hover:bg-red-200"
                                                @click="search_type = 'manually'; clear_results_and_errors()">
                                                    Enter Manually
                                                </button>
                                            </p>
                                        </div>

                                        <div class="mt-4 mb-2 text-gray border-b">Select from the list below</div>

                                        <div class="h-screen-40 overflow-auto">
                                            <ul class="multiple-results-list"></ul>
                                        </div>

                                    </div>

                                </div>

                                {{-- MLS Search Div --}}
                                <div class="max-w-xs mx-auto"
                                x-show.transition="search_type === 'mls'">

                                    <div>
                                        <x-elements.input
                                        id="mls_search_input"
                                        placeholder=""
                                        data-label="Enter MLS ID"
                                        :size="'xl'"/>
                                    </div>

                                    <div class="w-full text-center mt-4">
                                        <x-elements.button
                                            class="mls-search"
                                            :buttonClass="'primary'"
                                            :buttonSize="'lg'"
                                            type="button"
                                            @click="get_property_info($event.currentTarget, search_type)">
                                            Continue <i class="fal fa-arrow-right ml-2"></i>
                                        </x-elements.button>
                                    </div>

                                </div>

                                {{-- Manual Entry Div --}}
                                <div
                                x-show.transition="search_type === 'manually'">

                                    <div class="grid grid-cols-6 gap-4">

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="street_number"
                                            name="street_number"
                                            placeholder=""
                                            data-label="Street Number"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.select
                                            id="street_dir"
                                            name="street_dir"
                                            data-label="Street Direction"
                                            :size="'md'">
                                                <option value=""></option>
                                                <option value="N">N</option>
                                                <option value="S">S</option>
                                                <option value="E">E</option>
                                                <option value="W">W</option>
                                                <option value="NE">NE</option>
                                                <option value="SE">SE</option>
                                                <option value="NW">NW</option>
                                                <option value="SW">SW</option>
                                            </x-elements.select>
                                        </div>

                                        <div class="col-span-3">
                                            <x-elements.input
                                            id="street_name"
                                            name="street_name"
                                            placeholder=""
                                            data-label="Street Name"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="unit"
                                            name="unit"
                                            placeholder=""
                                            data-label="Unit"
                                            :size="'md'"/>
                                        </div>

                                    </div>

                                    <div class="grid grid-cols-6 gap-4">

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="zip"
                                            name="zip"
                                            placeholder=""
                                            data-label="Zip"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-2">
                                            <x-elements.input
                                            id="city"
                                            name="city"
                                            placeholder=""
                                            data-label="City"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.select
                                            id="state"
                                            name="state"
                                            data-label="State"
                                            :size="'md'">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                @endforeach
                                            </x-elements.select>
                                        </div>

                                        <div class="col-span-2">
                                            <x-elements.select
                                            id="county"
                                            name="county"
                                            data-label="County"
                                            :size="'md'"
                                            disabled>

                                            </x-elements.select>
                                        </div>

                                    </div>

                                </div>


                                {{-- Final Result --}}
                                <div class="border border-secondary mt-12 rounded text-gray-600"
                                id="final_results"
                                x-show.transition.in="final_result">

                                    <div class="bg-secondary text-white px-4 py-2"><i class="fal fa-check fa-lg mr-3"></i> Property Found</div>

                                    <div class="grid grid-cols-4 gap-2 p-4">

                                        <div class="col-span-1">
                                            <img src="" id="property_image" class="max-h-20 rounded-lg shadow"
                                            x-show.transition="property_found_mls">
                                            <i class="fad fa-home-alt fa-3x text-gray-400" id="no_property_image" x-show.transition="!property_found_mls"></i>
                                        </div>
                                        <div class="col-span-3 flex items-center">
                                            <div>
                                                <div class="text-gray-600" id="property_address"></div>
                                                <div class="flex justify-start items-center"
                                                x-show.transition="property_found_mls">
                                                    <div id="property_listing_id" class="text-gray-700"></div>
                                                    <div class="mx-3"> - </div>
                                                    <div id="property_status" class="text-lg font-semibold text-secondary"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="max-w-sm p-3 mx-auto">

                                        <div class=" text-gray-700"
                                        x-show.transition="property_found_mls">
                                            <div class="mb-2">
                                                Listing Office: <span id="property_list_office"></span>
                                            </div>
                                            <div class="mb-2">
                                                List Agent: <span id="property_list_agent"></span>
                                            </div>
                                            <div class="mb-2">
                                                List Date: <span id="property_list_date"></span>
                                            </div>
                                            <div class="mb-2">
                                                List Price: <span id="property_list_price"></span>
                                            </div>
                                            <div class="mb-2">
                                                Property Type: <span id="property_type"></span>
                                            </div>
                                        </div>
                                        <div x-show.transition="property_found_tax_records">
                                            <div class="mb-2"
                                            x-show="tax_records_link">
                                                Tax Records: <a href="" id="property_tax_records_link" target="_blank" class="text-primary">View Tax Records</a>
                                            </div>
                                            {{-- <div class="mb-2">
                                                Owners: <span id="property_owners"></span>
                                            </div> --}}
                                        </div>

                                        <div class="py-4 flex justify-around"
                                        :class="{ 'border-t': property_found_mls }">
                                            <x-elements.button
                                                class=""
                                                :buttonClass="'primary'"
                                                :buttonSize="'lg'"
                                                type="button"
                                                @click="active_step = 2">
                                                Next Step <i class="fal fa-arrow-right ml-2"></i>
                                            </x-elements.button>
                                        </div>

                                        <div class="mt-6 border-t pt-4 text-sm text-gray-400">
                                            Wrong Property?
                                            <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="search_type = 'manually'; clear_results_and_errors()"> Enter Manually </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- STEP 2 --}}
                        <div x-show.transition="active_step === '2'">
                            step 2
                        </div>

                        {{-- STEP 3 --}}
                        <div x-show.transition="active_step === '3'">

                            <div class="address-header text-gray-500 font-semibold text-xl mb-6"></div>

                            @if($transaction_type =='listing')

                                Listing stuff here

                            @elseif($transaction_type == 'contact')

                                Contract stuff here

                            @elseif($transaction_type == 'referral')

                                Referral stuff here

                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    @if(auth() -> user() -> group == 'agent')
    <input type="hidden" id="Agent_ID" value="{{ auth() -> user() -> agent -> id }}">
    @endif



</x-app-layout>
