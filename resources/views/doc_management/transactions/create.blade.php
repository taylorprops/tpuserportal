<x-app-layout>
    @section('title') Add {{ $details['header'] }} @endsection
    <x-slot name="header">
        <i class="{{ $details['icon'] }} mr-3"></i> Add {{ $details['header'] }}
    </x-slot>

    <div class="create-div pb-36 pt-16"
    x-data="create(`{{ $transaction_type }}`)"
    x-init="
        address_search();
        get_contacts();
        transaction_type = '{{ $transaction_type }}';
        if(transaction_type == 'listing') { this.show_both = true; };
        $fetch('/transactions/get_property_types').then(data => property_types = data);
        $fetch('/transactions/get_property_sub_types').then(data => property_sub_types = data);">

        <div class="max-w-screen-lg mx-auto sm:px-6 lg:px-12">

            {{-- STEPS --}}
            <nav aria-label="Progress">

                <ol class="space-y-4 md:flex md:space-y-0 md:space-x-8">

                    <li class="md:flex-1">
                        <a href="javascript:void(0)"
                        class="pl-4 py-2 flex flex-col border-l-4  md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4 border-secondary hover:border-secondary-dark"
                        @click="active_step = 1">
                            <span class="text-xs font-semibold tracking-wide uppercase"
                            :class="{ 'text-secondary-dark': active_step === 1, 'text-secondary-light ': active_step !== 1 }">Step 1</span>
                            <span class="text-sm text-secondary-dark font-medium mt-2"
                            :class="{ 'text-secondary-dark': active_step === 1, 'text-secondary-light ': active_step !== 1 }">Locate Property</span>
                        </a>
                    </li>

                    <li class="md:flex-1 @if($transaction_type == 'referral') hidden @endif">
                        <a href="javascript:void(0)"
                        class="pl-4 py-2 flex flex-col border-l-4 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4" aria-current="step"
                        :class="{ 'border-secondary hover:border-secondary-dark': active_step >= 2, 'border-gray-200 hover:border-gray-300': active_step === 1 }"
                        @click="if(steps_complete > 0) { active_step = 2 }">
                            <span class="text-xs font-semibold tracking-wide uppercase"
                            :class="{ 'text-secondary-light ': active_step === 3, 'text-gray-400': active_step === 1, 'text-secondary-dark': active_step === 2 }">Step 2</span>
                            <span class="text-sm font-medium mt-2"
                            :class="{ 'text-secondary-light': active_step === 3, 'text-gray-500': active_step === 1, 'text-secondary-dark': active_step === 2 }">
                                Enter Checklist Details
                            </span>
                        </a>
                    </li>

                    <li class="md:flex-1">
                        <a href="javascript:void(0)"
                        class="group pl-4 py-2 flex flex-col border-l-4 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4"
                        :class="{ 'border-secondary hover:border-secondary-dark': active_step === 3, 'border-gray-200 hover:border-gray-300': active_step !== 3 }"
                        @click="if(steps_complete > 1) { active_step = 3 }">
                            <span class="text-xs font-semibold tracking-wide uppercase"
                            :class="{ 'text-secondary-dark': active_step === 3, 'text-gray-400': active_step !== 3 }">Step @if($transaction_type == 'referral') 2 @else 3 @endif</span>
                            <span class="text-sm font-medium mt-2"
                            :class="{ 'text-secondary-dark': active_step === 3, 'text-gray-500': active_step !== 3 }">
                                Enter Required Details
                            </span>
                        </a>
                    </li>

                </ol>

            </nav>

            <hr class="text-primary my-5">


            <div class="py-6 md:py-8">

                {{-- STEP 1 --}}
                <div x-transition="active_step === 1">

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
                    <div class="py-6 md:pb-16 bg-white">

                        <div class="flex justify-around mt-4 mb-8">
                            <div class="w-3/4 bg-secondary-lightest border-l-4 border-secondary text-secondary p-4 flex items-center"
                            x-show="address_not_found">
                                <p><i class="fad fa-info-circle mr-3"></i> Please confirm the address.</p>
                            </div>
                        </div>

                        {{-- Address Search Div --}}
                        <div class="md:w-2/3 mx-auto"
                        x-transition="search_type === 'address'">

                            <div class="grid grid-cols-1 md:grid-cols-7">

                                <div class="col-span-1 md:col-span-6">
                                    <x-elements.input
                                    id="address_search_input"
                                    placeholder=""
                                    data-label="Enter the Street Address"
                                    :size="'lg'"
                                    @keydown="show_street_error = false"/>
                                </div>
                                <div class="col-span-1 md:ml-2">
                                    <x-elements.input
                                    id="address_search_unit"
                                    placeholder=""
                                    data-label="Unit"
                                    :size="'lg'"/>
                                </div>

                            </div>

                            <div class="mt-4">
                                <x-elements.button
                                    class="address-search"
                                    :buttonClass="'primary'"
                                    :buttonSize="'lg'"
                                    type="button"
                                    @click="get_property_info($event.currentTarget, search_type)"
                                    @keydown="sessionStorage.search_details = ''">
                                    Continue <i class="fal fa-arrow-right ml-2"></i>
                                </x-elements.button>
                            </div>



                        </div>

                        {{-- MLS Search Div --}}
                        <div class="md:max-w-xs mx-auto"
                        x-transition="search_type === 'mls'">

                            <div>
                                <x-elements.input
                                id="mls_search_input"
                                placeholder=""
                                data-label="Enter MLS ID"
                                :size="'lg'"/>
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
                        x-transition="search_type === 'manually'">

                            <form id="manual_entry_form">


                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                                    <div class="col-span-1">
                                        <x-elements.input
                                        class="required"
                                        id="street_number"
                                        name="street_number"
                                        placeholder=""
                                        data-label="Street Number"
                                        :size="'lg'"/>
                                    </div>

                                    <div class="col-span-1 md:col-span-4">
                                        <x-elements.input
                                        class="required"
                                        id="street_name"
                                        name="street_name"
                                        placeholder=""
                                        data-label="Street Name"
                                        :size="'lg'"/>
                                    </div>

                                    <div class="col-span-1">
                                        <x-elements.input
                                        id="unit"
                                        name="unit"
                                        placeholder=""
                                        data-label="Unit"
                                        :size="'lg'"/>
                                    </div>

                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-7">

                                    <div class="col-span-1">
                                        <x-elements.input
                                        class="required"
                                        id="zip"
                                        name="zip"
                                        placeholder=""
                                        data-label="Zip"
                                        :size="'lg'"
                                        @keyup="get_location_details('#manual_entry_form', '', $event.target, '#city', '#state', '#county')"/>
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <x-elements.input
                                        class="required"
                                        id="city"
                                        name="city"
                                        placeholder=""
                                        data-label="City"
                                        :size="'lg'"/>
                                    </div>

                                    <div class="col-span-1">
                                        <x-elements.select
                                        class="required"
                                        id="state"
                                        name="state"
                                        data-label="State"
                                        :size="'lg'"
                                        @change="let value = $event.target.value; $fetch('/transactions/get_counties/'+value).then(data => counties = data);">
                                            <option value=""></option>
                                            @foreach($states as $state)
                                                <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                            @endforeach
                                        </x-elements.select>
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <x-elements.select
                                        class="required"
                                        id="county"
                                        name="county"
                                        data-label="County"
                                        :size="'lg'">
                                        <template
                                        x-for="county in counties">
                                            <option :value="county" x-text="county"></option>
                                        </template>
                                        </x-elements.select>
                                    </div>

                                </div>

                                <div class="h-32 flex items-center justify-around">
                                    <x-elements.button
                                        class=""
                                        :buttonClass="'primary'"
                                        :buttonSize="'lg'"
                                        type="button"
                                        @click="save_manual_entry()">
                                        Next Step <i class="fal fa-arrow-right ml-2"></i>
                                    </x-elements.button>
                                </div>

                            </form>

                        </div>


                        {{-- Errors --}}

                        <div class="mt-8"
                        x-transition="show_no_property_error">
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
                        x-transition="show_street_error">
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
                        x-transition="show_license_state_error">
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                <p class="font-bold">Error</p>
                                <p>You can only add properties from states the company is licesned in.</p>
                            </div>
                        </div>



                        {{-- Final Result --}}
                        <div class="border border-secondary mt-12 rounded w-2/3 mx-auto"
                        id="final_results"
                        x-transition="final_result">

                            <div class="bg-secondary text-white px-4 py-2"><i class="fal fa-check fa-lg mr-3"></i> Property Found</div>

                            <div class="grid grid-cols-4 gap-2 p-4">

                                <div class="col-span-1">
                                    <img src="" id="property_image" class="max-h-20 rounded-lg shadow"
                                    x-transition="property_found_mls">
                                    <i class="fad fa-home-alt fa-3x text-gray-400" id="no_property_image" x-transition="!property_found_mls"></i>
                                </div>
                                <div class="col-span-3 flex items-center">
                                    <div>
                                        <div id="property_address"></div>
                                        <div class="flex justify-start items-center"
                                        x-transition="property_found_mls">
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
                                x-transition="property_found_mls">
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
                                        Property Type: <span id="property_type_display"></span>
                                    </div>
                                </div>
                                <div x-transition="property_found_tax_records">
                                    <div class="mb-2"
                                    x-show="tax_records_link">
                                        Tax Records: <a href="" id="property_tax_records_link" target="_blank" class="text-primary">View Tax Records</a>
                                    </div>
                                    {{-- <div class="mb-2">
                                        Owners: <span id="property_owners"></span>
                                    </div> --}}
                                </div>

                                <div class="pb-4 pt-8 flex justify-around"
                                :class="{ 'border-t': property_found_mls }">
                                    <x-elements.button
                                        class=""
                                        :buttonClass="'primary'"
                                        :buttonSize="'lg'"
                                        type="button"
                                        @click="set_checklist_details(); active_step = 2; steps_complete = 1;">
                                        Next Step <i class="fal fa-arrow-right ml-2"></i>
                                    </x-elements.button>
                                </div>

                                <div class="mt-6 border-t pt-4 text-sm text-gray-400 flex justify-around">
                                    <div>
                                        Wrong Property?
                                        <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="search_type = 'manually'; clear_results_and_errors()"> Enter Manually </a>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- STEP 2 --}}
                <div x-transition="active_step === 2">

                    <div class="address-header text-secondary-dark font-semibold text-xl mb-6"></div>

                    <form id="checklist_details_div">

                        <div class="text-lg my-4">Please provide the following details</div>

                        @if(auth() -> user() -> group != 'agent')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="mb-7">
                                    <x-elements.select
                                    class="required"
                                    id="Agent_ID"
                                    name="Agent_ID"
                                    data-label="Select Agent"
                                    :size="'md'">
                                        <option value=""></option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent -> id }}">{{ $agent -> first_name }} {{ $agent -> last_name }}</option>
                                        @endforeach

                                    </x-elements.select>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="Agent_ID" value="{{ auth() -> user() -> agent -> id }}">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                            <div>
                                <x-elements.select
                                id="SaleRent"
                                name="SaleRent"
                                class="required"
                                data-label="Transaction Type"
                                :size="'md'"
                                @change="for_sale = $event.target.value == 'sale' || $event.target.value == 'both' ? 'yes' : 'no'; $nextTick(() => { property_type_selected() })">
                                    <option value="sale" :selected="for_sale === 'yes'">Sale</option>
                                    <option value="rental" :selected="for_sale === 'no'">Rental</option>
                                    <option value="both" :class="{ 'hidden': show_both === false }">Both</option>

                                </x-elements.select>
                            </div>

                            <div>
                                <x-elements.select
                                id="PropertyType"
                                name="PropertyType"
                                class="required"
                                data-label="Property Type"
                                :size="'md'"
                                @change="property_type_selected()">
                                    <option value=""></option>
                                    <template x-for="prop_type in property_types" :key="prop_type.id">
                                        <option :value="prop_type.property_type" x-text="prop_type.property_type"></option>
                                    </template>

                                </x-elements.select>
                            </div>

                            <div
                            x-show="for_sale === 'yes'">
                                <x-elements.select
                                id="PropertySubType"
                                name="PropertySubType"
                                class="required"
                                data-label="Sale Type"
                                :size="'md'"
                                @change="property_type_selected()">
                                    <option value=""></option>
                                    <template x-for="property_sub_type in property_sub_types" :key="property_sub_type.id">
                                        <option :value="property_sub_type.property_sub_type" x-text="property_sub_type.property_sub_type"></option>
                                    </template>

                                </x-elements.select>
                            </div>

                            <div
                            x-show="show_disclosures === true && for_sale === 'yes'">
                                <x-elements.input
                                type="text"
                                class="required numbers-only"
                                id="YearBuilt"
                                name="YearBuilt"
                                placeholder=""
                                data-label="Year Built"
                                :size="'md'"/>
                            </div>

                            <div
                            x-show="property_type !== 'Commercial' && property_type !== 'New Construction' && show_disclosures === true && for_sale === 'yes'">
                                <x-elements.select
                                id="HoaCondoFees"
                                name="HoaCondoFees"
                                class="required"
                                data-label="HOA/Condo Fees"
                                :size="'md'">
                                    <option value=""></option>
                                    <option value="hoa">HOA Fees</option>
                                    <option value="condo">Condo Fees</option>
                                    <option value="none">None</option>

                                </x-elements.select>
                            </div>

                        </div>

                        <div class="flex justify-around py-12 w-full">
                            <x-elements.button
                            class=""
                            :buttonClass="'primary'"
                            :buttonSize="'lg'"
                            type="button"
                            @click="check_checklist_details()">
                            Next Step <i class="fal fa-arrow-right ml-2"></i>
                        </x-elements.button>
                        </div>

                    </form>


                </div>

                {{-- STEP 3 --}}
                <div x-transition="active_step === 3">

                    <div class="address-header text-secondary-dark font-semibold text-xl mb-6"></div>

                    <div>

                        @if($transaction_type =='listing')

                            <form id="create_form">

                                <div class="text-xl p-3 mb-5 border-b border-t-4 text-gray-700 seller-header"
                                x-text="for_sale === 'yes' ? 'Seller(s)' : 'Owner(s)'"></div>

                                <div class="lg:w-5/6 mx-auto">

                                    <div class="members-container" data-type="seller">

                                        <div class="border rounded p-4 mt-3 member-container" data-id="1">

                                            <div class="flex justify-between">

                                                <div class="text-secondary text-lg mb-2"><span x-text="for_sale === 'yes' ? 'Seller' : 'Owner'"></span> <span class="member-id">1</span></div>

                                                <x-elements.button
                                                class="mb-3"
                                                :buttonClass="'primary'"
                                                :buttonSize="'sm'"
                                                type="button"
                                                @click="show_add_contact_modal = true; import_contact_member_id = 1">
                                                <i class="fad fa-user-friends mr-2"></i> Import from Contacts
                                                </x-elements.button>

                                            </div>

                                            <div class="py-5">
                                                <x-elements.check-box
                                                :size="'sm'"
                                                :color="'blue'"
                                                :label="'Owner is a Trust, Company or other Entity'"
                                                @click="seller_is_trust = !seller_is_trust"/>
                                            </div>

                                            <div class="my-3"
                                            x-transition="seller_is_trust">
                                                <x-elements.input
                                                class="member-entity-name"
                                                data-label="Trust, Company or other Entity Name"
                                                :size="'md'"
                                                x-bind:class="{ 'required': seller_is_trust }"/>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                                <div>
                                                    <x-elements.input
                                                    class="member-first required"
                                                    data-label="First Name"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }"/>
                                                </div>

                                                <div>
                                                    <x-elements.input
                                                    class="member-last required"
                                                    data-label="Last Name"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }"/>
                                                </div>

                                                <div>
                                                    <x-elements.input
                                                    class="member-phone phone required"
                                                    data-label="Phone"
                                                    :size="'md'"/>
                                                </div>

                                                <div>
                                                    <x-elements.input
                                                    class="member-email"
                                                    data-label="Email"
                                                    :size="'md'"/>
                                                </div>

                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-7 gap-5 mt-5">

                                                <div class="col-span-1 md:col-span-3">
                                                    <x-elements.input
                                                    class="member-street required"
                                                    data-label="Home Address"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }"/>
                                                </div>

                                                <div class="col-span-1">
                                                    <x-elements.input
                                                    class="numbers-only member-zip required"
                                                    data-label="Zip Code"
                                                    data-member-index="1"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }"
                                                    @keyup="get_location_details('.member-container', '1', $event.target, '.member-city', '.member-state')"/>
                                                </div>

                                                <div class="col-span-1 md:col-span-2">
                                                    <x-elements.input
                                                    class="member-city required"
                                                    data-label="City"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }"/>
                                                </div>

                                                <div class="col-span-1">
                                                    <x-elements.select
                                                    class="member-state required"
                                                    data-label="State"
                                                    :size="'md'"
                                                    x-bind:class="{ 'required': !seller_is_trust }">
                                                        <option value=""></option>
                                                        @foreach($states as $state)
                                                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                        @endforeach
                                                    </x-elements.select>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="my-4">
                                        <x-elements.button
                                        :buttonClass="'primary'"
                                        :buttonSize="'sm'"
                                        type="button"
                                        @click="add_member('Seller')">
                                        <i class="fal fa-plus mr-2"></i> <span x-text="for_sale === 'yes' ? 'Add Seller' : 'Add Owner'"></span>
                                    </x-elements.button>
                                    </div>

                                </div>

                                <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700">Listing Details</div>

                                <div class="lg:w-5/6 mx-auto mb-10">

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                        <div class="col-span-1">
                                            <x-elements.input
                                            class="required numbers-only money"
                                            id="ListPrice"
                                            name="ListPrice"
                                            placeholder=""
                                            data-label="List Price"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="MLSListDate"
                                            name="MLSListDate"
                                            type="date"
                                            class="required"
                                            data-label="List Date"
                                            :size="'md'"
                                            @onchange="document.getElementById('ExpirationDate').setAttribute('min', $event.target.value)"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ExpirationDate"
                                            name="ExpirationDate"
                                            type="date"
                                            class="required"
                                            data-label="Expiration Date"
                                            :size="'md'"/>
                                        </div>

                                    </div>

                                </div>

                                <div class="w-full flex justify-around py-10 border-t">
                                    <x-elements.button
                                    :buttonClass="'primary'"
                                    :buttonSize="'lg'"
                                    type="button"
                                    @click="save_transaction($event.currentTarget, 'listing')">
                                    <i class="fal fa-check mr-2"></i> Save Listing
                                </x-elements.button>
                                </div>

                            </form>

                        @elseif($transaction_type == 'contract')

                        <form id="create_form">

                            <div class="text-xl p-3 mb-5 border-b border-t-4 text-gray-700 buyer-header"
                            x-text="for_sale === 'yes' ? 'Buyer(s)' : 'Renter(s)'"></div>

                            <div class="lg:w-5/6 mx-auto">

                                <div class="members-container" data-type="buyer">

                                    <div class="border rounded p-4 mt-3 member-container" data-id="1">

                                        <div class="flex justify-between">

                                            <div class="text-secondary text-lg mb-2"><span x-text="for_sale === 'yes' ? 'Buyer' : 'Renter'"></span> <span class="member-id">1</span></div>

                                            <x-elements.button
                                            class="mb-3"
                                            :buttonClass="'primary'"
                                            :buttonSize="'sm'"
                                            type="button"
                                            @click="show_add_contact_modal = true; import_contact_member_id = 1">
                                            <i class="fad fa-user-friends mr-2"></i> Import from Contacts
                                            </x-elements.button>

                                        </div>

                                        <div class="py-5">
                                            <x-elements.check-box
                                            :size="'sm'"
                                            :color="'blue'"
                                            :label="'Client is a Trust, Company or other Entity'"
                                            @click="buyer_is_trust = !buyer_is_trust"/>
                                        </div>

                                        <div class="my-3"
                                        x-transition="buyer_is_trust">
                                            <x-elements.input
                                            class="contact-entity-name"
                                            data-label="Trust, Company or other Entity Name"
                                            :size="'md'"
                                            x-bind:class="{ 'required': buyer_is_trust }"/>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                            <div>
                                                <x-elements.input
                                                class="member-first required"
                                                data-label="First Name"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }"/>
                                            </div>

                                            <div>
                                                <x-elements.input
                                                class="member-last required"
                                                data-label="Last Name"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }"/>
                                            </div>

                                            <div>
                                                <x-elements.input
                                                class="member-phone phone required"
                                                data-label="Phone"
                                                :size="'md'"/>
                                            </div>

                                            <div>
                                                <x-elements.input
                                                class="member-email"
                                                data-label="Email"
                                                :size="'md'"/>
                                            </div>

                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-7 gap-5 mt-5">

                                            <div class="col-span-1 md:col-span-3">
                                                <x-elements.input
                                                class="member-street required"
                                                data-label="Home Address"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }"/>
                                            </div>

                                            <div class="col-span-1">
                                                <x-elements.input
                                                class="numbers-only member-zip required"
                                                data-label="Zip Code"
                                                data-member-index="1"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }"
                                                @keyup="get_location_details('.member-container', '1', $event.target, '.member-city', '.member-state')"/>
                                            </div>

                                            <div class="col-span-1 md:col-span-2">
                                                <x-elements.input
                                                class="member-city required"
                                                data-label="City"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }"/>
                                            </div>

                                            <div class="col-span-1">
                                                <x-elements.select
                                                class="member-state required"
                                                data-label="State"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !buyer_is_trust }">
                                                    <option value=""></option>
                                                    @foreach($states as $state)
                                                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                    @endforeach
                                                </x-elements.select>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="my-4">
                                    <x-elements.button
                                    :buttonClass="'primary'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="add_member('Buyer')">
                                    <i class="fal fa-plus mr-2"></i> Add <span x-text="for_sale === 'yes' ? 'Buyer' : 'Renter'"></span>
                                </x-elements.button>
                                </div>

                            </div>


                            <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700 seller-header"
                            x-text="for_sale === 'yes' ? 'Seller(s)' : 'Owner(s)'"></div>

                            <div class="lg:w-5/6 mx-auto">

                                <div class="members-container" data-type="seller">

                                    <div class="border rounded p-4 mt-3 member-container" data-id="1">

                                        <div class="text-secondary text-lg mb-2"><span x-text="for_sale === 'yes' ? 'Seller' : 'Owner'"></span> <span class="member-id">1</span></div>

                                        <div class="py-5">
                                            <x-elements.check-box
                                            :size="'sm'"
                                            :color="'blue'"
                                            :label="'Owner is a Trust, Company or other Entity'"
                                            @click="seller_is_trust = !seller_is_trust"/>
                                        </div>

                                        <div class="my-3"
                                        x-transition="seller_is_trust">
                                            <x-elements.input
                                            class="contact-entity-name"
                                            data-label="Trust, Company or other Entity Name"
                                            :size="'md'"
                                            x-bind:class="{ 'required': seller_is_trust }"/>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                            <div>
                                                <x-elements.input
                                                class="member-first required"
                                                data-label="First Name"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !seller_is_trust }"/>
                                            </div>

                                            <div>
                                                <x-elements.input
                                                class="member-last required"
                                                data-label="Last Name"
                                                :size="'md'"
                                                x-bind:class="{ 'required': !seller_is_trust }"/>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="my-4">
                                    <x-elements.button
                                    :buttonClass="'primary'"
                                    :buttonSize="'sm'"
                                    type="button"
                                    @click="add_member('Seller', true)">
                                        <i class="fal fa-plus mr-2"></i> Add <span x-text="for_sale === 'yes' ? 'Seller' : 'Owner'"><span>
                                    </x-elements.button>
                                </div>

                            </div>


                            <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700">List Agent</div>

                            <div class="lg:w-5/6 mx-auto">

                                <div class="relative lg:w-1/2">
                                    <x-elements.input
                                    class="bg-gray-50 focus:bg-white agent-search-input"
                                    placeholder="Search Agents in Bright MLS"
                                    :size="'sm'"
                                    @keyup="agent_search($event.target.value)"/>
                                    <i class="fal fa-search text-gray-400 absolute right-3 top-5"></i>
                                </div>

                                <div class="relative">
                                    <div class="border bg-white p-2 absolute top-1 z-50"
                                    x-show="show_agent_search_results"
                                    @click.outside="show_agent_search_results = false">
                                        <ul id="agent_search_results" class="max-h-300-px overflow-auto"></ul>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-5">

                                    <div class="col-span-1">
                                        <x-elements.input
                                        class="required"
                                        id="ListAgentFirstName"
                                        name="ListAgentFirstName"
                                        data-label="Agent First Name"
                                        :size="'md'"/>
                                    </div>

                                    <div>
                                        <x-elements.input
                                        class="required"
                                        id="ListAgentLastName"
                                        name="ListAgentLastName"
                                        data-label="Agent Last Name"
                                        :size="'md'"/>
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <x-elements.input
                                        class="required"
                                        id="ListOfficeName"
                                        name="ListOfficeName"
                                        data-label="Agent Company"
                                        :size="'md'"/>
                                    </div>

                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 md:mt-5">

                                    <div>
                                        <x-elements.input
                                        class="phone required"
                                        id="ListAgentPreferredPhone"
                                        name="ListAgentPreferredPhone"
                                        data-label="Agent Phone"
                                        :size="'md'"/>
                                    </div>

                                    <div class="col-span-1 md:col-span-2">
                                        <x-elements.input
                                        id="ListAgentEmail"
                                        name="ListAgentEmail"
                                        data-label="Agent Email"
                                        :size="'md'"/>
                                    </div>

                                    <div>
                                        <x-elements.input
                                        id="ListAgentMlsId"
                                        name="ListAgentMlsId"
                                        data-label="Agent MLS ID"
                                        :size="'md'"/>
                                    </div>

                                </div>

                                <input type="hidden" id="ListOfficeFullStreetAddress" name="ListOfficeFullStreetAddress">
                                <input type="hidden" id="ListOfficeCity" name="ListOfficeCity">
                                <input type="hidden" id="ListOfficeStateOrProvince" name="ListOfficeStateOrProvince">
                                <input type="hidden" id="ListOfficePostalCode" name="ListOfficePostalCode">

                            </div>


                            <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700"
                            x-text="for_sale === 'yes' ? 'Contract Details' : 'Lease Details'"></div>

                            <div class="lg:w-5/6 mx-auto mb-10">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                    <div class="col-span-1"
                                    x-show="for_sale === 'no'">
                                        <x-elements.input
                                        class="required numbers-only money"
                                        id="LeaseAmount"
                                        name="LeaseAmount"
                                        placeholder=""
                                        data-label="Lease Amount"
                                        :size="'md'"/>
                                    </div>

                                    <div class="col-span-1"
                                    x-show="for_sale === 'yes'">
                                        <x-elements.input
                                        class="required numbers-only money"
                                        id="ContractPrice"
                                        name="ContractPrice"
                                        placeholder=""
                                        data-label="Contract Price"
                                        :size="'md'"/>
                                    </div>

                                    <div class="col-span-1"
                                    x-show="for_sale === 'yes'">
                                        <x-elements.input
                                        id="ContractDate"
                                        name="ContractDate"
                                        type="date"
                                        class="required"
                                        data-label="Contract Date"
                                        :size="'md'"/>
                                    </div>

                                    <div class="col-span-1">
                                        <x-elements.input
                                        id="CloseDate"
                                        name="CloseDate"
                                        type="date"
                                        class="required"
                                        data-label="Settlement Date"
                                        :size="'md'"/>
                                    </div>

                                </div>

                            </div>


                            <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700"
                            x-show="for_sale === 'yes'">Title and Earnest</div>

                            <div class="lg:w-5/6 mx-auto mb-10"
                            x-show="for_sale === 'yes'">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                    <div class="col-span-1">

                                        <x-elements.select
                                        class="required"
                                        id="UsingHeritageTitle"
                                        name="UsingHeritageTitle"
                                        data-label="Using Heritage Title"
                                        :size="'md'"
                                        @change="using_heritage_title = $event.currentTarget.value == 'yes' ? true : false">
                                            <option value=""></option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                            <option value="maybe">Not Sure Yet</option>
                                        </x-elements.select>
                                    </div>

                                    <div class="col-span-3">
                                        <div>
                                            <x-elements.input
                                            id="TitleCompany"
                                            name="TitleCompany"
                                            placeholder=""
                                            data-label="Title Company"
                                            :size="'md'"
                                            x-bind:value="using_heritage_title == true ? 'Heritage Title' : ''"/>
                                        </div>
                                    </div>

                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-5">

                                    <div class="col-span-1">
                                        <x-elements.input
                                        id="EarnestAmount"
                                        name="EarnestAmount"
                                        type="text"
                                        class="money numbers-only"
                                        data-label="Earnest Amount"
                                        :size="'md'" />
                                    </div>

                                    <div class="col-span-2">
                                        <x-elements.select
                                        class=""
                                        id="EarnestHeldBy"
                                        name="EarnestHeldBy"
                                        data-label="Earenest Held By"
                                        :size="'md'">
                                            <option value=""></option>
                                            <option value="us">Taylor/Anne Arundel Properties</option>
                                            <option value="other_company">Other Real Estate Company</option>
                                            <option value="title">Title Company/Attorney</option>
                                            <option value="heritage_title">Heritage Title</option>
                                            <option value="builder">Builder</option>
                                        </x-elements.select>
                                    </div>

                                </div>

                            </div>



                            <div class="w-full flex justify-around py-10 border-t">
                                <x-elements.button
                                    :buttonClass="'primary'"
                                    :buttonSize="'lg'"
                                    type="button"
                                    @click="save_transaction($event.currentTarget, 'contract')">
                                    <i class="fal fa-check mr-2"></i> <span x-text="for_sale === 'yes' ? 'Save Contract' : 'Save Lease'"></span>
                                </x-elements.button>
                            </div>

                        </form>

                        @elseif($transaction_type == 'referral')

                            <form id="create_form">

                                <div class="text-xl p-3 mb-5 mt-5 border-b border-t-4 text-gray-700">Client Info</div>

                                <div class="lg:w-5/6 mx-auto mb-10">

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                                        <div>
                                            <x-elements.input
                                            id="ReferralClientFirstName"
                                            name="ReferralClientFirstName"
                                            class="required"
                                            data-label="First Name"
                                            :size="'md'"/>
                                        </div>

                                        <div>
                                            <x-elements.input
                                            id="ReferralClientLastName"
                                            name="ReferralClientLastName"
                                            class="required"
                                            data-label="Last Name"
                                            :size="'md'"/>
                                        </div>

                                        <div>
                                            <x-elements.input
                                            id="ReferralClientPhone"
                                            name="ReferralClientPhone"
                                            class="phone required"
                                            data-label="Phone"
                                            :size="'md'"/>
                                        </div>

                                    </div>

                                    <div class="mt-6 mb-3">
                                        <x-elements.button
                                        class=""
                                        :buttonClass="'primary'"
                                        :buttonSize="'sm'"
                                        type="button"
                                        @click="set_referral_address()">
                                        Use Property Address
                                    </x-elements.button> - <span class="address-header text-xs"></span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-7 gap-5 mt-5 referral-address-container">

                                        <div class="col-span-1 md:col-span-3">
                                            <x-elements.input
                                            id="ReferralClientStreet"
                                            name="ReferralClientStreet"
                                            class="required"
                                            data-label="Home Address"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralClientZip"
                                            name="ReferralClientZip"
                                            class="numbers-only required"
                                            data-label="Zip Code"
                                            data-member-index="1"
                                            :size="'md'"
                                            x-bind:class="{ 'required': !seller_is_trust }"
                                            @keyup="get_location_details('.referral-address-container', '', $event.target, '#ReferralClientCity', '#ReferralClientState')"/>
                                        </div>

                                        <div class="col-span-1 md:col-span-2">
                                            <x-elements.input
                                            id="ReferralClientCity"
                                            name="ReferralClientCity"
                                            class="required"
                                            data-label="City"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.select
                                            id="ReferralClientState"
                                            name="ReferralClientState"
                                            class="required"
                                            data-label="State"
                                            :size="'md'">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                @endforeach
                                            </x-elements.select>
                                        </div>

                                    </div>

                                </div>


                                <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700">Receiving Agent</div>

                                <div class="lg:w-5/6 mx-auto">

                                    <div class="relative lg:w-1/2">
                                        <x-elements.input
                                        class="bg-gray-50 focus:bg-white agent-search-input"
                                        placeholder="Search Agents in Bright MLS"
                                        :size="'sm'"
                                        @keyup="agent_search($event.target.value)"/>
                                        <i class="fal fa-search text-gray-400 absolute right-3 top-5"></i>
                                    </div>

                                    <div class="relative">
                                        <div class="border bg-white p-2 absolute top-1 z-50"
                                        x-show="show_agent_search_results"
                                        @click.outside="show_agent_search_results = false">
                                            <ul id="agent_search_results" class="max-h-300-px overflow-auto"></ul>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-5">

                                        <div class="col-span-1">
                                            <x-elements.input
                                            class="required"
                                            id="ReferralReceivingAgentFirstName"
                                            name="ReferralReceivingAgentFirstName"
                                            data-label="Agent First Name"
                                            :size="'md'"/>
                                        </div>

                                        <div>
                                            <x-elements.input
                                            class="required"
                                            id="ReferralReceivingAgentLastName"
                                            name="ReferralReceivingAgentLastName"
                                            data-label="Agent Last Name"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1 md:col-span-2">
                                            <x-elements.input
                                            class="required"
                                            id="ReferralReceivingAgentOfficeName"
                                            name="ReferralReceivingAgentOfficeName"
                                            data-label="Agent Company"
                                            :size="'md'"/>
                                        </div>

                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-7 gap-5 mt-5 referral-office-address-container">

                                        <div class="col-span-1 md:col-span-3">
                                            <x-elements.input
                                            id="ReferralReceivingAgentOfficeStreet"
                                            name="ReferralReceivingAgentOfficeStreet"
                                            class="required"
                                            data-label="Office Address"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralReceivingAgentOfficeZip"
                                            Zip="ReferralReceivingAgentOfficeZip"
                                            class="numbers-only required"
                                            data-label="Office Zip"
                                            data-member-index="1"
                                            :size="'md'"
                                            @keyup="get_location_details('.referral-office-address-container', '', $event.target, '#ReferralReceivingAgentOfficeCity', '#ReferralReceivingAgentOfficeState')"/>
                                        </div>

                                        <div class="col-span-1 md:col-span-2">
                                            <x-elements.input
                                            id="ReferralReceivingAgentOfficeCity"
                                            name="ReferralReceivingAgentOfficeCity"
                                            class="required"
                                            data-label="City"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.select
                                            id="ReferralReceivingAgentOfficeState"
                                            name="ReferralReceivingAgentOfficeState"
                                            class="required"
                                            data-label="State"
                                            :size="'md'">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                                @endforeach
                                            </x-elements.select>
                                        </div>

                                    </div>

                                </div>


                                <div class="text-xl p-3 mb-5 mt-20 border-b border-t-4 text-gray-700">Commission Details</div>

                                <div class="lg:w-5/6 mx-auto">

                                    <div class="grid grid-cols-4 gap-5">

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralSettlementDate"
                                            name="ReferralSettlementDate"
                                            type="date"
                                            class="required"
                                            data-label="Settlement Date"
                                            :size="'md'"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralCommissionAmount"
                                            name="ReferralCommissionAmount"
                                            type="text"
                                            class="money-decimal required"
                                            data-label="Total Commission Amount"
                                            :size="'md'"
                                            @keyup="total_referral_commission()"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralReferralPercentage"
                                            name="ReferralReferralPercentage"
                                            type="text"
                                            class="numbers-only required"
                                            data-label="Referral Percentage"
                                            max-length="2"
                                            :size="'md'"
                                            @keyup="total_referral_commission(); let ele = $event.target; ele.value = ele.value.replace(/\./, '');"/>
                                        </div>

                                        <div class="col-span-1">
                                            <x-elements.input
                                            id="ReferralAgentCommission"
                                            name="ReferralAgentCommission"
                                            type="text"
                                            class="money-decimal required"
                                            readonly
                                            data-label="Your Commission Total"
                                            :size="'md'"/>
                                        </div>

                                    </div>

                                </div>

                                <div class="w-full flex justify-around pt-12 border-t mt-8">
                                    <x-elements.button
                                        :buttonClass="'primary'"
                                        :buttonSize="'lg'"
                                        type="button"
                                        @click="save_transaction($event.currentTarget, 'referral')">
                                        <i class="fal fa-check mr-2"></i> Save Referral
                                    </x-elements.button>
                                </div>

                            </form>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        <x-modals.modal
        :modalWidth="'sm:w-1/3'"
        :modalTitle="'Import Contact'"
        :modalId="'show_add_contact_modal'"
        x-show="show_add_contact_modal">

            <table id="contacts_table" class="data-table hover nowrap order-column row-border" width="100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </x-modals.modal>

    </div>

    <template id="member_template">

        <div class="border rounded p-4 mt-3 new-member-div opacity-100 transition-all duration-700 ease-in-out" data-id="%%member_id%%">

            <div class="flex justify-between">

                <div class="text-secondary text-lg mb-2">%%member_type%% <span class="member-id">%%member_count%%</span></div>

                <div>

                    <x-elements.button
                    :buttonClass="'primary'"
                    :buttonSize="'sm'"
                    type="button"
                    @click="show_add_contact_modal = true; import_contact_member_id = %%member_id%%">
                    <i class="fad fa-user-friends mr-2"></i> Import from Contacts
                    </x-elements.button>

                    <x-elements.button
                    class="py-1.5 delete-button"
                    :buttonClass="'danger'"
                    :buttonSize="'sm'"
                    type="button"
                    @click="remove_member(%%member_id%%, $event.currentTarget);">
                    <i class="fal fa-times"></i>
                    </x-elements.button>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                <div>
                    <x-elements.input
                    class="member-first required"
                    data-label="First Name"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

                <div>
                    <x-elements.input
                    class="member-last required"
                    data-label="Last Name"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

                <div>
                    <x-elements.input
                    class="member-phone phone required"
                    data-label="Phone"
                    :size="'md'"/>
                </div>

                <div>
                    <x-elements.input
                    class="member-email"
                    data-label="Email"
                    :size="'md'"/>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-7 gap-5 mt-5">

                <div class="col-span-1 md:col-span-3">
                    <x-elements.input
                    class="member-street required"
                    data-label="Home Address"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

                <div class="col-span-1">
                    <x-elements.input
                    class="numbers-only member-zip required"
                    data-label="Zip Code"
                    data-member-index="%%member_id%%"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"
                    @keyup="get_location_details('.member-container', '%%member_id%%', $event.target, '.member-city', '.member-state')"/>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <x-elements.input
                    class="member-city required"
                    data-label="City"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

                <div class="col-span-1">
                    <x-elements.select
                    class="member-state required"
                    data-label="State"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }">
                        <option value=""></option>
                        @foreach($states as $state)
                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                        @endforeach
                    </x-elements.select>
                </div>

            </div>

        </div>

    </template>

    <template id="member_seller_for_contract_template">

        <div class="border rounded p-4 mt-3 new-member-div opacity-100 transition-all duration-700 ease-in-out" data-id="%%member_id%%">

            <div class="flex justify-between">

                <div class="text-secondary text-lg mb-2">%%member_type%% <span class="member-id">%%member_count%%</span></div>

                <div>

                    <x-elements.button
                    class="py-1.5 delete-button"
                    :buttonClass="'danger'"
                    :buttonSize="'sm'"
                    type="button"
                    @click="remove_member(%%member_id%%, $event.currentTarget);">
                    <i class="fal fa-times"></i>
                    </x-elements.button>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

                <div>
                    <x-elements.input
                    class="member-first required"
                    data-label="First Name"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

                <div>
                    <x-elements.input
                    class="member-last required"
                    data-label="Last Name"
                    :size="'md'"
                    x-bind:class="{ 'required': !seller_is_trust }"/>
                </div>

            </div>

        </div>

    </template>

    <template id="agent_search_result_template">
        <li class="px-2 py-3 border-b cursor-pointer hover:text-secondary"
        x-on:click="select_agent($event.currentTarget)"
        data-MemberFirstName="%%MemberFirstName%%"
        data-MemberLastName="%%MemberLastName%%"
        data-MemberPreferredPhone="%%MemberPreferredPhone%%"
        data-OfficePhone="%%OfficePhone%%"
        data-MemberEmail="%%MemberEmail%%"
        data-OfficeName="%%OfficeName%%"
        data-MemberMlsId="%%MemberMlsId%%"
        data-OfficeAddress1="%%OfficeAddress1%%"
        data-OfficeCity="%%OfficeCity%%"
        data-OfficeStateOrProvince="%%OfficeStateOrProvince%%"
        data-OfficePostalCode="%%OfficePostalCode%%">
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <div class="font-semibold">%%MemberLastName%%, %%MemberFirstName%%</div>
                    <div class="text-sm">%%MemberType%% (%%MemberMlsId%%)<br>%%MemberEmail%%</div>
                </div>
                <div>
                    <div class="font-semibold">%%OfficeName%%</div>
                    <div class="text-sm">%%OfficeMlsId%%</div>
                </div>
                <div>
                    <div class="text-sm">%%OfficeAddress1%%<br>%%OfficeCity%%, %%OfficeStateOrProvince%% %%OfficePostalCode%%</div>
                </div>
            </div>
        </li>
    </template>

</x-app-layout>


