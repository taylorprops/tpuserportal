@php
$title = 'Address Database';
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

    <div class="pb-64 pt-2">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12"
        x-data="address_database()">

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mt-12">

                <div class="border-4 p-4 rounded-lg col-span-2">

                    <form id="options_form">

                        <div class="flex justify-between items-center mb-4">

                            <div class="text-xl font-semibold">
                                List Options
                            </div>

                            <div x-show="list_type === 'addresses' || list_group == 'test_center_selected'">
                                <div>
                                    <button type="button" class="button primary md"
                                    @click="get_results()">Get Results <i class="fa fa-share ml-2"></i></button>
                                </div>
                            </div>

                            <div class="flex justify-end items-center space-x-3" x-show="list_type === 'emails' && list_group != 'test_center_selected'">
                                <div>Get List For:</div>
                                <div>
                                    <button type="button" class="button primary md"
                                    @click="get_results('mailchimp')">MailChimp <i class="fa fa-share ml-2"></i></button>
                                </div>
                                <div>
                                    <button type="button" class="button primary md"
                                    @click="get_results('sendinblue')">Send In Blue <i class="fa fa-share ml-2"></i></button>
                                </div>
                                <div>
                                    <button type="button" class="button primary md"
                                    @click="get_results('omnisend')">Omnisend <i class="fa fa-share ml-2"></i></button>
                                </div>

                            </div>

                        </div>

                        <div class="my-6 p-4 border rounded-lg">

                            <div class="text-lg font-medium text-gray-900 mb-2">List Type</div>

                            <p class="text-sm leading-5 text-gray-500">Who is the audience?</p>

                            <fieldset class="mt-2">

                                <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_group"
                                        value="agents"
                                        data-label="Agents"
                                        checked
                                        @click="agents_selected(); clear_results(); location_data();">
                                    </div>

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_group"
                                        value="test_center"
                                        data-label="Test Center Agents"
                                        @click="test_center_selected(); clear_results();">
                                    </div>

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_group"
                                        value="loan_officers"
                                        data-label="Loan Officers"
                                        @click="loan_officers_selected(); clear_results(); location_data();">
                                    </div>

                                </div>

                            </fieldset>

                            <p class="text-sm leading-5 text-gray-500 mt-4">Is this for Emails or Home Addresses?</p>

                            <fieldset class="mt-2">

                                <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">

                                    <div class="">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_type"
                                        value="email"
                                        data-label="Emails"
                                        checked
                                        @change="search_offices(); agents_selected(); clear_results(); list_type = 'emails'"
                                        x-ref="email_input">
                                    </div>

                                    <div class="" x-ref="address_input_div">
                                        <input type="radio"
                                        class="form-element radio lg primary"
                                        name="list_type"
                                        value="address"
                                        data-label="Home Addresses"
                                        @change="search_offices(); clear_results(); list_type = 'addresses'"
                                        x-ref="address_input">
                                    </div>

                                </div>

                            </fieldset>

                        </div>

                        <div class="my-6 p-4 border rounded-lg">

                            <div class="text-lg font-medium text-gray-900 mb-2">Location</div>

                            <div class="flex flex-wrap justify-start">

                                <div class="">

                                    <div class="text-gray-500 mb-3">States</div>

                                    <div class="p-2 rounded bg-gray-50 w-64 min-h-150-px max-h-300-px overflow-y-auto">

                                        @foreach($states as $state)

                                            @php
                                            $disabled_los = null;
                                            if(!in_array($state, $states_loan_officers)) {
                                                $disabled_los = true;
                                            }
                                            $disabled_test_center = null;
                                            if(!in_array($state, $states_test_center)) {
                                                $disabled_test_center = true;
                                            }

                                            @endphp

                                            <div class="state_div @if($disabled_los) disabled_loan_officer @endif @if($disabled_test_center) disabled_test_center @endif">

                                                <input type="checkbox"
                                                class="form-element checkbox lg primary"
                                                name="states[]"
                                                value="{{ $state }}"
                                                data-label="{{ $state }}"
                                                @if($state == 'MD') checked @endif
                                                @click="location_data('{{ $state }}'); clear_results();">

                                            </div>

                                        @endforeach

                                    </div>

                                    <div class="mt-2 flex justify-between">
                                        <div class="ml-2.5">
                                            <input type="checkbox"
                                            class="form-element checkbox lg primary"
                                            data-label="Select All"
                                            x-ref="select_all_states"
                                            @click="select_all_options('states', $el.checked); clear_results();">
                                        </div>
                                        <span class="bg-yellow-600 rounded-full py-1 px-3 text-xs text-white inline-block"><span id="state_count"></span> Selected</span>
                                    </div>

                                </div>

                                <div class="ml-8" x-show="list_group != 'test_center_selected'">

                                    <div class="text-gray-500 mb-3">Counties</div>


                                    <div class="p-2 rounded bg-gray-50 w-96 min-h-150-px max-h-300-px overflow-y-auto">

                                        <template
                                        x-for="county in counties">
                                            <div class="county-checkbox">
                                                <input type="checkbox"
                                                class="form-element checkbox lg primary"
                                                name="counties[]"
                                                :data-state="county.state"
                                                :data-label="county.state+' - '+county.county"
                                                :value="county.state+'-'+county.county"
                                                @click="search_offices(); update_details(); clear_results();">
                                            </div>
                                        </template>

                                    </div>

                                    <div class="mt-2 flex justify-between">
                                        <div class="ml-2.5">
                                            <input type="checkbox"
                                            x-ref="select_all_counties"
                                            class="form-element checkbox lg primary"
                                            data-label="Select All"
                                            @click="select_all_options('counties', $el.checked); clear_results();">
                                        </div>
                                        <span class="bg-yellow-600 rounded-full py-1 px-3 text-xs text-white inline-block"><span id="county_count"></span> Selected</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="my-6 p-4 border rounded-lg"
                        :class="{ 'opacity-20': list_group == 'loan_officers' }"
                        x-show="list_group != 'test_center_selected'">

                            <div class="text-lg font-medium text-gray-900 mb-2">Offices</div>

                            <div class="flex justify-start items-end">

                                <div class="max-w-xs">
                                    <input type="text"
                                    id="office_search"
                                    class="form-element input md"
                                    placeholder="Search..."
                                    data-label="Office Name"
                                    @input.debounce="search_offices(); clear_results();"
                                    :disabled="list_group == 'loan_officers' ? true : false"
                                    x-ref="office_name">
                                </div>

                                <div class="ml-2 mb-1.5">
                                    <button type="button" class="button secondary sm" @click="$refs.office_name.value = ''; clear_office_search_results();">Clear <i class="fal fa-times ml-2"></i></button>
                                </div>

                            </div>

                            <div id="office_search_results" x-ref="office_search"></div>

                        </div>

                    </form>

                </div>


                <div class="col-span-1">

                    <div class="border-4 border-green-200 p-4 rounded-lg relative">

                        <div class="mb-4 text-xl font-semibold">
                            Results
                        </div>

                        <div class="h-full ml-4" id="results_div" x-ref="results_div"></div>

                    </div>

                    <div class="border-4 border-blue-200 mt-12 p-4 rounded-lg relative">

                        <div class="mb-4 text-xl font-semibold">
                            Recently Added Agent Emails
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <input type="date" class="form-element input md" x-ref="recently_added_emails_start" data-label="Start Date">
                            </div>

                            <div>
                                <input type="date" class="form-element input md" x-ref="recently_added_emails_end" data-label="End Date">
                            </div>

                        </div>

                        <div class="mt-6 flex justify-around">
                            <button type="button" class="button primary lg" @click="get_recently_added()"><i class="fa fa-download mr-2"></i> Download Recently Added</button>
                        </div>

                        <div class="mt-6">
                            <div class="mb-3 text-xl font-semibold">Recent Additions</div>
                            <div class="max-h-200-px overflow-y-auto">
                                @foreach($recently_added_emails as $email)
                                    <div class="p-2 mb-2  border-b grid grid-cols-2 text-sm">
                                        <div>{{ $email -> date_added }}</div>
                                        <div>{{ $email -> added }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>

                    <div class="border-4 border-red-200 mt-12 p-4 rounded-lg relative">

                        <div class="mb-4 text-xl font-semibold">
                            Purged Agent Emails
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <input type="date" class="form-element input md" x-ref="purged_emails_start" data-label="Start Date">
                            </div>

                            <div>
                                <input type="date" class="form-element input md" x-ref="purged_emails_end" data-label="End Date">
                            </div>

                        </div>

                        <div class="mt-6 flex justify-around">
                            <button type="button" class="button primary lg" @click="get_purged()"><i class="fa fa-download mr-2"></i> Download Purged</button>
                        </div>

                        <div class="mt-6">
                            <div class="mb-3 text-xl font-semibold">Recent Purges</div>
                            <div class="max-h-200-px overflow-y-auto">
                                @foreach($purged_emails as $email)
                                    <div class="p-2 mb-2  border-b grid grid-cols-2 text-sm">
                                        <div>{{ $email -> date_purged }}</div>
                                        <div>{{ $email -> purged }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
