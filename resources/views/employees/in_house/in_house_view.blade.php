@php
$title = $employee ? $employee -> fullname : 'Add In House Employee';
$breadcrumbs = [['In House Employees', '/employees/in_house'], [$title]];
@endphp
<x-app-layout>
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
            :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pt-2 pb-48"
        x-data="profile('{{ $employee -> id ?? null }}', 'in_house', @if ($employee && $employee -> photo_location != '') true @else false @endif, '');">

        <div class="max-w-1000-px mx-auto pt-8 md:pt-12 xl:pt-16 px-4">

            <div>

                <div class="sm:hidden">
                    @if ($employee)
                        <label for="tabs" class="sr-only">Select a tab</label>
                        <select id="tabs" name="tabs" class="block w-full focus:ring-primary focus:border-primary border-gray-300 rounded-md"
                            @change="active_tab = $el.value">
                            <option selected value="1">Details</option>
                            <option value="2">Documents</option>
                            <option value="3">Photo</option>
                            <option value="4">Notes</option>
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

                            @if ($employee)
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
                                    <span>Photo</span>
                                </a>

                                <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                                    :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4', 'border-primary text-primary-dark': active_tab === '4' }"
                                    @click="active_tab = '4'">
                                    <i class="fad fa-notes mr-3"
                                        :class="{ 'text-primary': active_tab === '4', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4' }"></i>
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

                                @if ($employee)
                                    <div class="col-span-1 m-2 sm:m-3"
                                        x-data="{ active: '{{ $employee -> active ?? 'yes' }}' }">
                                        <select
                                            class="form-element select md required"
                                            id="active"
                                            name="active"
                                            data-label="Active"
                                            x-model="active"
                                            :class="{ 'bg-green-50': active === 'yes', 'bg-red-50': active === 'no' }">
                                            <option value="yes" @if ($employee && $employee -> active == 'yes') selected @endif>Yes</option>
                                            <option value="no" @if ($employee && $employee -> active == 'no') selected @endif>No</option>
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

                                @if ($employee)
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

                                @if ($employee)
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4">
                                @endif


                                <div class="m-2 sm:m-3">
                                    <select
                                        class="form-element select md"
                                        id="job_title"
                                        name="job_title"
                                        class="required"
                                        data-label="Position">
                                        <option value=""></option>
                                        <option value="Admin Assistant" @if ($employee && $employee -> job_title == 'Admin Assistant') selected @endif>Admin Assistant</option>
                                        <option value="Marketing" @if ($employee && $employee -> job_title == 'Marketing') selected @endif>Marketing</option>
                                        <option value="Manager" @if ($employee && $employee -> job_title == 'Manager') selected @endif>Manager</option>
                                    </select>
                                </div>

                                @if (auth() -> user() -> level == 'super_admin')
                                    <div class="m-2 sm:m-3">
                                        <select
                                            class="form-element select md required"
                                            id="emp_position"
                                            name="emp_position"
                                            data-label="Website Level">
                                            <option value=""></option>
                                            <option value="admin" @if ($employee && $employee -> emp_position == 'admin') selected @endif>Admin</option>
                                            <option value="owner" @if ($employee && $employee -> emp_position == 'owner') selected @endif>Owner</option>
                                            <option value="manager" @if ($employee && $employee -> emp_position == 'manager') selected @endif>Manager</option>
                                            <option value="super_admin" @if ($employee && $employee -> emp_position == 'super_admin') selected @endif>Super Admin</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="emp_position" value="admin">
                                @endif

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
                                        @foreach ($states as $state)
                                            <option value="{{ $state -> state }}" @if ($employee && $employee -> address_state == $state -> state) selected @endif>{{ $state -> state }}</option>
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
                                                value="@if ($employee) {{ \Crypt::decrypt($employee -> soc_sec) }} @endif"
                                                @focus="show_ssn = true">
                                        </div>
                                        <div class="absolute top-6 right-3" @click="show_ssn = !show_ssn">
                                            <i class="fad fa-eye"></i>
                                        </div>
                                    </div>

                                </div>

                            </div>



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

                    @if ($employee)
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

                        </div>

                        <div x-show="active_tab === '4'" class="pt-12">

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
                That company email address is already in use. Please use a different email address.
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
                    class="form-element select md"
                    name="license_state[]"
                    data-label="State">
                    <option value=""></option>
                    @foreach ($states as $state)
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
