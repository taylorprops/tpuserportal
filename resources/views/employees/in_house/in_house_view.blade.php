@php
$title = $employee ? $employee -> fullname : 'Add In House Employee';
$breadcrumbs = [
    ['In House Employees', '/employees/in_house'],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pt-2 pb-48"
    x-data="profile('{{ $employee -> id ?? null }}', 'in_house', @if($employee && $employee -> photo_location != '') true @else false @endif, '');">

        <div class="max-w-900-px mx-auto pt-8 md:pt-12 xl:pt-16 px-4">

            <div class="">

                <div class="text-xl font-medium text-gray-700 border-b mb-6">Details</div>

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
                                <option value="admin" @if($employee && $employee -> emp_position == 'admin') selected @endif>Admin</option>
                                <option value="super_admin" @if($employee && $employee -> emp_position == 'super_admin') selected @endif>Super Admin</option>
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
                                <option value="Admin Assistant" @if($employee && $employee -> job_title == 'Admin Assistant') selected @endif>Admin Assistant</option>
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

                <div class="text-xl font-medium text-gray-700 border-b mb-6 mt-12 sm:mt-24">Employee Docs</div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <div class="border rounded-md p-4 max-w-600-px col-span-1 md:col-span-2">
                        <div class="docs-div"></div>
                    </div>

                    <div class="col-span-1">
                        <div class="text-gray mb-3">Add Documents</div>
                        <input
                        type="file"
                        class="form-element input md"
                        id="employee_docs"
                        name="employee_docs"
                        multiple>
                    </div>

                </div>

                <div class="">

                    <div class="text-xl font-medium text-gray-700 border-b mb-6 mt-12 sm:mt-24">Employee Photo</div>

                    <div class="flex justify-start items-center max-w-500-px">

                        <div>

                            <div class="flex justify-around items-center">
                                <i class="fad fa-user fa-4x text-primary"
                                x-show="!has_photo"></i>
                                <img class="rounded-lg shadow max-h-36" id="employee_image" src="{{ $employee -> photo_location_url ?? null }}"
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

            @endif

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
