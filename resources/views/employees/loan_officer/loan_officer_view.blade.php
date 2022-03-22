@php
$title = $employee ? $employee -> fullname : 'Add Heritage Financial Employee';
$breadcrumbs = [
    ['Loan Officers', '/employees/loan_officer'],
    [$title],
];

$hidden_from_processor = auth() -> user() -> level == 'processor' ? 'hidden' : '';
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pt-2 pb-48"
    x-data="profile('{{ $employee -> id ?? null }}', 'mortgage', @if($employee && $employee -> photo_location != '') true @else false @endif, '#bio', 'www.heritagefinancial.com');">

        <div class="max-w-1000-px mx-auto pt-8 md:pt-12 xl:pt-16 px-4">

            <div>

                <div class="sm:hidden">
                    @if($employee)
                    <label for="tabs" class="sr-only">Select a tab</label>
                    <select id="tabs" name="tabs" class="block w-full focus:ring-primary focus:border-primary border-gray-300 rounded-md"
                    @change="active_tab = $el.value">
                        <option selected value="1">Details</option>
                        <option value="2">Documents</option>
                        <option value="3">Bio/Photo</option>
                        <option value="4">Billing</option>
                        <option value="5">Notes</option>
                    </select>
                    @endif
                </div>

                <div class="hidden sm:block">

                    <div class="border-b border-gray-200">

                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '1', 'border-primary text-primary-dark': active_tab === '1' }"
                            @click="active_tab = '1'">
                                <i class="fad fa-user mr-3"
                                :class="{ 'text-primary': active_tab === '1', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '1' }"></i>
                                <span>Details</span>
                            </a>

                            @if($employee)
                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2', 'border-primary text-primary-dark': active_tab === '2' }"
                            @click="active_tab = '2'">
                                <i class="fad fa-copy mr-3"
                                :class="{ 'text-primary': active_tab === '2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2' }"></i>
                                <span>Documents</span>
                            </a>

                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3', 'border-primary text-primary-dark': active_tab === '3' }"
                            @click="active_tab = '3'">
                                <i class="fad fa-portrait mr-3"
                                :class="{ 'text-primary': active_tab === '3', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3' }"></i>
                                <span>Photo/Bio</span>
                            </a>

                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4', 'border-primary text-primary-dark': active_tab === '4' }"
                            @click="active_tab = '4'">
                                <i class="fad fa-credit-card mr-3"
                                :class="{ 'text-primary': active_tab === '4', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4' }"></i>
                                <span>Billing</span>
                            </a>
                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '5', 'border-primary text-primary-dark': active_tab === '5' }"
                            @click="active_tab = '5'">
                                <i class="fad fa-notes mr-3"
                                :class="{ 'text-primary': active_tab === '5', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '5' }"></i>
                                <span>Notes</span>
                            </a>
                            @endif
                        </nav>

                    </div>

                </div>

                <div>

                    <div x-show="active_tab === '1'" class="pt-4 sm:pt-12">

                        <form id="employee_form" autocomplete="off">

                            <div class="grid grid-cols-1 sm:grid-cols-4">

                                @if($employee)
                                    <div class="col-span-1 m-2 sm:m-3"
                                    x-data="{ active: '{{ $employee -> active ?? 'yes' }}' }">
                                        <select
                                        class="form-element select md required"
                                        id="active"
                                        name="active"
                                        data-label="Active"
                                        x-model="active"
                                        :class="{ 'bg-green-50': active === 'yes', 'bg-red-50': active === 'no' }">
                                            <option value="yes" @if($employee && $employee -> active == 'yes') selected @endif>Yes</option>
                                            <option value="no" @if($employee && $employee -> active == 'no') selected @endif>No</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="active" id="active" value="yes">
                                @endif

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="date"
                                    class="form-element input md required"
                                    id="start_date"
                                    name="start_date"
                                    data-label="Start Date"
                                    value="{{ $employee -> start_date ?? null }}">
                                </div>

                                @if($employee)
                                    <div class="col-span-1 m-2 sm:m-3">
                                        <input
                                        type="date"
                                        class="form-element input md"
                                        id="term_date"
                                        name="term_date"
                                        data-label="Termination Date"
                                        value="{{ $employee -> term_date ?? null }}">
                                    </div>
                                @else
                                    <input type="hidden" name="term_date" id="term_date" value="">
                                @endif

                            @if($employee)
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4">
                            @endif
                                <div class="m-2 sm:m-3">
                                    <select
                                    class="form-element select md required"
                                    id="emp_position"
                                    name="emp_position"
                                    data-label="Position">
                                        <option value=""></option>
                                        <option value="loan_officer" @if($employee && $employee -> emp_position == 'loan_officer') selected @endif>Loan Officer</option>
                                        <option value="processor" @if($employee && $employee -> emp_position == 'processor') selected @endif>Processor</option>
                                        <option value="manager" @if($employee && $employee -> emp_position == 'manager') selected @endif>Manager</option>
                                    </select>
                                </div>

                                <div class="m-2 sm:m-3">
                                    <select
                                    class="form-element select md"
                                    id="job_title"
                                    name="job_title"
                                    class="required"
                                    data-label="Job Title">
                                        <option value=""></option>
                                        <option value="Loan Officer" @if($employee && $employee -> job_title == 'Loan Officer') selected @endif>Loan Officer</option>
                                        <option value="Senior Loan Officer" @if($employee && $employee -> job_title == 'Senior Loan Officer') selected @endif>Senior Loan Officer</option>
                                        <option value="Processor" @if($employee && $employee -> job_title == 'Processor') selected @endif>Processor</option>
                                        <option value="Manager" @if($employee && $employee -> job_title == 'Manager') selected @endif>Manager</option>
                                    </select>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="first_name"
                                    name="first_name"
                                    data-label="First Name"
                                    value="{{ $employee -> first_name ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="last_name"
                                    name="last_name"
                                    data-label="Last Name"
                                    value="{{ $employee -> last_name ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md phone required"
                                    id="phone"
                                    name="phone"
                                    data-label="Phone"
                                    value="{{ $employee -> phone ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1 lg:col-span-2">
                                    <input
                                    type="email"
                                    class="form-element input md required"
                                    id="email"
                                    name="email"
                                    data-label="Email"
                                    value="{{ $employee -> email ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-8">

                                <div class="m-2 sm:m-3 sm:col-span-2 xl:col-span-4">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="address_street"
                                    name="address_street"
                                    data-label="Street"
                                    value="{{ $employee -> address_street ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="address_zip"
                                    name="address_zip"
                                    data-label="Zip"
                                    value="{{ $employee -> address_zip ?? null }}"
                                    x-on:keyup="get_location_details('#employee_form', '', '#address_zip', '#address_city', '#address_state');">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1 xl:col-span-2">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="address_city"
                                    name="address_city"
                                    data-label="City"
                                    value="{{ $employee -> address_city ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1">
                                    <select
                                    class="form-element select md required"
                                    id="address_state"
                                    name="address_state"
                                    data-label="State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            <option value="{{ $state -> state }}" @if($employee && $employee -> address_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="date"
                                    class="form-element input md required"
                                    id="dob"
                                    name="dob"
                                    data-label="DOB"
                                    value="{{ $employee -> dob ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3">
                                    <div class="relative">
                                        <div class="absolute top-0 left-0">
                                            <input
                                            type="password"
                                            :type="show_ssn === true ? 'text' : 'password'"
                                            class="form-element input md ssn required"
                                            id="soc_sec"
                                            name="soc_sec"
                                            data-label="Social Security"
                                            value="@if($employee){{ \Crypt::decrypt($employee -> soc_sec) }}@endif"
                                            @focus="show_ssn = true">
                                        </div>
                                        <div class="absolute top-6 right-3" @click="show_ssn = !show_ssn">
                                            <i class="fad fa-eye"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6 {{ $hidden_from_processor }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 {{ $hidden_from_processor }}">

                                <div class="m-2 sm:m-3 flex items-end">
                                    <div>
                                        <input
                                        type="text"
                                        class="form-element input md numbers-only required"
                                        id="commission_percent"
                                        name="commission_percent"
                                        data-label="Commission Split Percent"
                                        value="{{ $employee -> commission_percent ?? '0' }}">
                                    </div>
                                    <i class="fal fa-percent ml-1 mb-2"></i>
                                </div>



                                <div class="m-2 sm:m-3 flex items-end">
                                    <div>
                                        <input
                                        type="text"
                                        class="form-element input md numbers-only required"
                                        id="loan_amount_percent"
                                        name="loan_amount_percent"
                                        data-label="Loan Amount Percent"
                                        value="{{ $employee -> loan_amount_percent ?? '0.00' }}">
                                    </div>
                                    <i class="fal fa-percent ml-1 mb-2"></i>
                                </div>

                            </div>

                            <div class="mt-4 mb-1 ml-4 sm:ml-5 text-sm italic text-gray-500 {{ $hidden_from_processor }}">
                                Manager Bonus
                            </div>

                            <div class="flex justify-start items-center flex-space-x-4 {{ $hidden_from_processor }}">

                                @php
                                $bonus_type = 'standard';
                                if($employee) {
                                    if($employee -> manager_bonus != '0') {
                                        $bonus_type = 'other';
                                    }
                                }
                                @endphp

                                <div class="flex ml-2 sm:ml-3"
                                x-data="{ bonus_type: '{{ $bonus_type }}' }">

                                    <div class="flex-1 border rounded py-1 px-2 mr-4"
                                    :class="{ 'bg-primary-lightest text-primary-dark': bonus_type === 'standard' }">
                                        <div class="pb-1 border-b"
                                        :class="{ 'border-primary-dark': bonus_type === 'standard' }">
                                            <input type="radio" class="form-element radio sm primary"
                                            name="manager_bonus_type"
                                            id="manager_bonus_type_1"
                                            value="standard"
                                            data-label="Standard"
                                            @if($bonus_type == 'standard') checked @endif
                                            @click="bonus_type = document.querySelector('[name=manager_bonus_type]:checked').value; $refs.manager_bonus.value = '0';">
                                        </div>
                                        <div class="h-20 flex items-center">
                                            <label for="manager_bonus_type_1" class="cursor-pointer">
                                                <div class="text-sm">
                                                    3% - Loan Officer Leads<br>
                                                    5% - Office Leads
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex-1 border rounded py-1 px-2 mr-4"
                                    :class="{ 'bg-primary-lightest text-primary-dark': bonus_type === 'other' }">
                                        <div class="pb-1 border-b"
                                        :class="{ 'border-primary-dark': bonus_type === 'other' }">
                                            <input type="radio" class="form-element radio sm primary"
                                            name="manager_bonus_type"
                                            id="manager_bonus_type_2"
                                            x-ref="other"
                                            value="other"
                                            data-label="Other"
                                            @if($bonus_type == 'other') checked @endif
                                            @click="bonus_type = document.querySelector('[name=manager_bonus_type]:checked').value;">
                                        </div>
                                        <div>
                                            <label for="manager_bonus_type_2" class="block cursor-pointer">
                                                <div class="m-2 sm:m-3 flex items-end">
                                                    <div>
                                                        <input
                                                        type="text"
                                                        class="form-element input md numbers-only required"
                                                        name="manager_bonus"
                                                        data-label="Manager Bonus Percent"
                                                        value="{{ $employee -> manager_bonus ?? '0' }}"
                                                        x-ref="manager_bonus"
                                                        @focus="$refs.other.click()">
                                                    </div>
                                                    <i class="fal fa-percent ml-1 mb-2"></i>
                                                </div>
                                            </label>
                                        </div>
                                    </div>


                                </div>


                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md numbers-only"
                                    id="nmls_id"
                                    name="nmls_id"
                                    data-label="NMLS ID"
                                    value="{{ $employee -> nmls_id ?? null }}">
                                </div>

                                <div class="m-2 sm:ml-4 col-span-1 sm:col-span-2 lg:col-span-3 border p-2 rounded-lg">
                                    <div class="flex justify-between">
                                        <div class="text-gray-800 m-2">Licenses</div>

                                        <button
                                        type="button"
                                        class="button primary sm"
                                        x-on:click="add_license()">
                                            <i class="fal fa-plus mr-2"></i> Add License
                                        </button>
                                    </div>
                                    <div class="licenses-div"></div>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="folder"
                                    name="folder"
                                    data-label="Profile Link Name"
                                    value="{{ $employee -> folder ?? null }}"
                                    @keyup="show_profile_link()">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1 sm:col-span-2 lg:col-span-3">
                                    <div class="text-gray-600 italic text-sm mt-1">Profile Link</div>
                                    <div class="mt-2">
                                        <a href="javascript:void(0)" class="text-primary" id="folder_url"></a>
                                    </div>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input md required"
                                    id="floify_folder"
                                    name="floify_folder"
                                    data-label="Floify Loan App Name"
                                    value="{{ $employee -> floify_folder ?? null }}"
                                    @keyup="show_floify_link()">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1 sm:col-span-2 lg:col-span-3">
                                    <div class="text-gray-600 italic text-sm mt-1">Floify Loan App Link</div>
                                    <div class="mt-2">
                                        <a href="javascript:void(0)" class="text-primary" id="floify_folder_url"></a>
                                    </div>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">


                            <div class="flex justify-around items-center mt-12">

                                <button
                                type="button"
                                class="button primary xl px-8 py-4 text-2xl"
                                    x-on:click="save_details($el)">
                                    <i class="fal fa-check mr-2"></i> Save
                                </button>
                            </div>

                        </form>

                    </div>

                    @if($employee)
                    <div x-show="active_tab === '2'" class="pt-12">

                        <div class="mb-8">
                            <div class="text-gray mb-3">Add Documents</div>
                            <input
                            type="file"
                            class="form-element input md"
                            id="employee_docs"
                            name="employee_docs"
                            multiple>
                        </div>

                        <div class="mt-12 mb-3">Uploaded Documents</div>
                        <div class="border rounded-md p-4">
                            <div class="docs-div"></div>
                        </div>

                    </div>

                    <div x-show="active_tab === '3'" class="pt-12">


                        <div class="flex justify-start items-center mb-8">

                            <div>

                                <div class="flex justify-around items-center">
                                    <i class="fad fa-user fa-4x text-primary"
                                    x-show="!has_photo"></i>
                                    <img class="rounded-lg shadow max-h-36" id="employee_image" src="{{ $employee -> photo_location_url }}"
                                    x-show="has_photo">
                                </div>


                                <button
                                type="button"
                                class="button danger sm mt-4"
                                x-on:click="delete_photo()"
                                x-show="has_photo">
                                    <i class="fal fa-times mr-2"></i> Delete Photo
                                </button>

                            </div>

                            <div class="flex-grow ml-4 lg:mx-12">
                                <div class="text-gray mb-3">Add/Replace Photo</div>
                                <input type="file" id="employee_photo" name="employee_photo">

                                <x-modals.modal
                                :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
                                :modalTitle="'Crop Photo'"
                                :modalId="'show_cropper_modal'"
                                :clickOutside="'employee_photo_pond.removeFiles();'"
                                x-show="show_cropper_modal">

                                    <div class="crop-container max-h-96"></div>

                                    <hr>

                                    <div class="flex justify-around items-center p-4">

                                        <button
                                        type="button"
                                        class="button primary md"
                                        x-on:click="save_cropped_image($el)">
                                            <i class="fal fa-check mr-2"></i> Save Changes
                                        </button>
                                    </div>

                                </x-modals.modal>

                            </div>

                        </div>

                        <hr>

                        <div class="max-w-700-px pt-12">

                            <textarea class="form-element textarea md" id="bio" name="bio">{!! $employee -> bio !!}</textarea>

                            <div class="flex justify-around items-center mt-4">

                                <button
                                type="button"
                                class="button primary xl"
                                x-on:click="save_bio($el)">
                                    <i class="fal fa-check mr-2"></i> Save Bio
                                </button>
                            </div>

                        </div>

                    </div>

                    <div x-show="active_tab === '4'" class="pt-12">

                        <div class="grid grid-cols-1 lg:grid-cols-4">

                            <div class="flex justify-start col-span-1">
                                <button class="button primary xl"><i class="fal fa-plus mr-2"></i> Add Payment</button>
                            </div>

                            <div class="col-span-1 lg:col-span-3 mt-12 lg:mt-0 max-w-3xl border p-4 rounded-lg">
                                <div class="flex justify-between mb-6">
                                    <div class="font-medium text-gray-700">Credit Cards</div>
                                    <button class="button primary sm" @click="show_add_credit_card_modal = true"><i class="fal fa-plus mr-2"></i> Add Card</button>
                                </div>

                                <div id="credit_cards_div" class="max-h-60 overflow-y-auto"></div>

                            </div>

                        </div>

                    </div>

                    <div x-show="active_tab === '5'" class="pt-12">

                        <div class="">

                            <div class="max-w-600-px mt-20">

                                <div class="flex justify-between">
                                    <div class="font-medium text-xl">Notes</div>
                                    <div>
                                        <button type="button" class="button primary md"
                                        @click="show_add_notes = ! show_add_notes"
                                        x-show="show_add_notes === false">
                                            <i class="fal fa-plus mr-2"></i> Add Note
                                        </button>
                                        <button type="button" class="button danger md no-text"
                                        @click="show_add_notes = ! show_add_notes"
                                        x-show="show_add_notes === true">
                                            <i class="fal fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="border rounded-md p-4 my-3"
                                x-show="show_add_notes" x-transition>
                                    <form id="add_notes_form">
                                        <div>
                                            <textarea class="form-element textarea md" name="notes" id="notes"
                                            x-ref="notes"></textarea>
                                        </div>
                                        <div class="flex justify-around mt-3">
                                            <button type="button" class="button primary md"
                                            @click.prevent="add_notes($el)">
                                                Save Note <i class="fal fa-check ml-2"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>

                            <div class="border-t-2 mt-4 max-w-600-px"
                            x-ref="notes_div"></div>

                        </div>

                    </div>

                    @endif

                </div>

            </div>


        </div>


        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
        :modalTitle="''"
        :modalId="'show_email_error_modal'"
        x-show="show_email_error_modal">

            <div class="p-6 flex items-center justify-around text-red-700">
                <i class="fad fa-exclamation-circle mr-4 fa-2x"></i>
                <span class="error-message"></span>
            </div>

        </x-modals.modal>


        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 sm:w-2/3 md:w-1/2 lg:w-1/3'"
        :modalTitle="''"
        :modalId="'show_confirm_delete_credit_card'"
        x-show="show_confirm_delete_credit_card">

            <div class="flex items-center justify-around">
                <div class="p-3 flex items-center justify-start">
                    <i class="fad fa-exclamation-circle mr-4 fa-2x text-red-700"></i>
                    <span class="">Are you sure you want to delete this credit card?</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t flex justify-around">
                <button class="button danger md" id="cancel_delete_credit_card" @click="show_confirm_delete_credit_card = false;">Cancel <i class="fal fa-times ml-2"></i></button>
                <button class="button primary md" id="confirm_delete_credit_card" @click="delete_credit_card()">Yes, Delete <i class="fal fa-arrow-right ml-2"></i></button>
            </div>

        </x-modals.modal>


        <x-modals.modal
        :modalWidth="'w-full sm:w-128'"
        :modalTitle="'Add Credit Card'"
        :modalId="'show_add_credit_card_modal'"
        x-show="show_add_credit_card_modal">

            <div>

                <form id="add_credit_card_form" autocomplete="off">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                        <div>
                            <input type="text" class="form-element input md" id="first" name="first" placeholder="First Name">
                        </div>
                        <div>
                            <input type="text" class="form-element input md" id="last" name="last" placeholder="Last Name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 my-4">
                        <div class="col-span-1 sm:col-span-3">
                            <input type="text" class="form-element input md" id="number" name="number" placeholder="Card Number">
                        </div>
                        <div>
                            <input type="text" class="form-element input md  numbers-only" id="code" name="code" placeholder="CVV">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-5 gap-4 my-4">

                        <div class="col-span-1 sm:col-span-2">
                            <select
                            class="form-element select md"
                            id="expire_month"
                            name="expire_month">
                                <option value="">Expire Month</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <select
                            class="form-element select md"
                            id="expire_year"
                            name="expire_year">
                                <option value="">Expire Year</option>
                                @php
                                for($y = date('Y'); $y <= date('Y') + 20; $y++) {
                                    echo '<option value="'.$y.'">'.$y.'</option>';
                                }
                                @endphp
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 my-4">

                        <div class="col-span-1">
                            <input type="text" class="form-element input md  numbers-only" id="zip" name="zip" placeholder="Zip Code">
                        </div>

                        <div class="col-span-1 sm:col-span-3">
                            <input type="text" class="form-element input md " id="street" name="street" placeholder="Street Address">
                        </div>

                    </div>

                    <div id="add_card_error_div"
                    x-show="show_add_card_error_div">
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Error</p>
                            <p id="add_card_error_message"> </p>
                        </div>
                    </div>

                    <div class="flex justify-around items-center border-t mt-4 pt-4">
                        <button type="button" class="button primary lg"
                        @click="add_credit_card($el)"><i class="fal fa-check mr-2"></i> Save Credit Card</button>
                    </div>

                </form>

            </div>

        </x-modals.modal>

    </div>


    <template id="doc_template">
        <div class="flex justify-between items-center border-b mb-4">
            <div>
                <a href="%%url%%" target="_blank">%%file_name%%</a>
            </div>
            <div>

                <button
                type="button"
                class="button danger md"
                x-on:click="delete_doc(%%id%%)">
                    <i class="fal fa-times"></i>
                </button>
            </div>
        </div>
    </template>

    <template id="license_template">
        <div class="flex justify-start items-end license mt-3">
            <div class="mx-2 w-24">
                <select
                class="form-element select md required"
                name="license_state[]"
                data-label="State">
                    <option value=""></option>
                    @foreach($states as $state)
                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mx-2 w-36">
                <input
                type="text"
                class="form-element input md required"
                name="license_number[]"
                data-label="Number">
            </div>
            <div class="mx-2 w-28 pb-1">

                <button
                type="button"
                class="button danger md delete-license-button"
                x-on:click="delete_license($el)">
                    <i class="fal fa-times mr-2"></i> Delete
                </button>
            </div>

        </div>
    </template>

</x-app-layout>
