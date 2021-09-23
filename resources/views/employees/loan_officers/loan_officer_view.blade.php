<x-app-layout>
    @section('title') {{ $loan_officer -> fullname }} @endsection
    <x-slot name="header">
        {{ $loan_officer -> fullname }}
    </x-slot>

    <div class="pt-2 pb-48"
    x-data="loan_officer('{{ $loan_officer -> id }}', @if($loan_officer -> photo_location != '') true @else false @endif)">

        <div class="max-w-900-px mx-auto pt-8 md:pt-12 xl:pt-16 px-4">

            <div class="">

                <div class="text-xl font-medium text-gray-700 border-b mb-6">Details</div>

                <form id="loan_officer_form">

                    <div class="grid grid-cols-1 sm:grid-cols-4">

                        <div class="p-3 rounded-md ml-2 sm:ml-4 col-span-1"
                        x-data="{ active: '{{ $loan_officer -> active }}' }"
                        :class="{ 'bg-green-50': active === 'yes', 'bg-red-50': active === 'no' }">
                            <select
                            class="form-element select md required"
                            id="active"
                            name="active"
                            data-label="Active"
                            x-model="active">
                                <option value="yes" @if($loan_officer -> active == 'yes') selected @endif>Yes</option>
                                <option value="no" @if($loan_officer -> active == 'no') selected @endif>No</option>
                            </select>
                        </div>

                        <div class="col-span-1 m-2 sm:m-3">
                            <input
                            type="date"
                            class="form-element input md required"
                            id="start_date"
                            name="start_date"
                            data-label="Start Date"
                            value="{{ $loan_officer -> start_date }}">
                        </div>

                        <div class="col-span-1 m-2 sm:m-3">
                            <input
                            type="date"
                            class="form-element input md"
                            id="term_date"
                            name="term_date"
                            data-label="Termination Date"
                            value="{{ $loan_officer -> term_date }}">
                        </div>

                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4">

                        <div class="m-2 sm:m-3">
                            <select
                            class="form-element select md required"
                            id="position"
                            name="position"
                            data-label="Position">
                                <option value=""></option>
                                <option value="loan_officer" @if($loan_officer -> position == 'loan_officer') selected @endif>Loan Officer</option>
                                <option value="processor" @if($loan_officer -> position == 'processor') selected @endif>Processor</option>
                                <option value="manager" @if($loan_officer -> position == 'manager') selected @endif>Manager</option>
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
                                <option value="Loan Officer" @if($loan_officer -> job_title == 'Loan Officer') selected @endif>Loan Officer</option>
                                <option value="Senior Loan Officer" @if($loan_officer -> job_title == 'Senior Loan Officer') selected @endif>Senior Loan Officer</option>
                                <option value="Processor" @if($loan_officer -> job_title == 'Processor') selected @endif>Processor</option>
                                <option value="Manager" @if($loan_officer -> job_title == 'Manager') selected @endif>Manager</option>
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
                            value="{{ $loan_officer -> first_name }}">
                        </div>

                        <div class="m-2 sm:m-3">
                            <input
                            type="text"
                            class="form-element input md required"
                            id="last_name"
                            name="last_name"
                            data-label="Last Name"
                            value="{{ $loan_officer -> last_name }}">
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
                            value="{{ $loan_officer -> phone }}">
                        </div>

                        <div class="m-2 sm:m-3 col-span-1 lg:col-span-2">
                            <input
                            type="email"
                            class="form-element input md required"
                            id="email"
                            name="email"
                            data-label="Email (For All Communications)"
                            value="{{ $loan_officer -> email }}">
                        </div>

                        <div class="m-2 sm:m-3 lg:col-span-2">
                            <input
                            type="text"
                            class="form-element input md required"
                            id="company_email"
                            name="company_email"
                            type="email"
                            data-label="Company Email"
                            value="{{ $loan_officer -> company_email }}">
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
                            value="{{ $loan_officer -> address_street }}">
                        </div>

                        <div class="m-2 sm:m-3">
                            <input
                            type="text"
                            class="form-element input md required"
                            id="address_zip"
                            name="address_zip"
                            data-label="Zip"
                            value="{{ $loan_officer -> address_zip }}"
                            x-on:keyup="get_location_details('#loan_officer_form', '', '#address_zip', '#address_city', '#address_state');">
                        </div>

                        <div class="m-2 sm:m-3 col-span-1 xl:col-span-2">
                            <input
                            type="text"
                            class="form-element input md required"
                            id="address_city"
                            name="address_city"
                            data-label="City"
                            value="{{ $loan_officer -> address_city }}">
                        </div>

                        <div class="m-2 sm:m-3 col-span-1">
                            <select
                            class="form-element select md required"
                            id="address_state"
                            name="address_state"
                            data-label="State"
                            value="{{ $loan_officer -> address_state }}">
                                <option value=""></option>
                                @foreach($states as $state)
                                    <option value="{{ $state -> state }}" @if($loan_officer -> address_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <hr class="bg-gray-300 my-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                        <div class="m-2 sm:m-3">
                            <input
                            type="text"
                            class="form-element input md numbers-only required"
                            id="commission_split"
                            name="commission_split"
                            data-label="Commission Split"
                            value="{{ $loan_officer -> commission_split }}">
                        </div>

                        <div class="m-2 sm:m-3">
                            <input
                            type="date"
                            class="form-element input md required"
                            id="dob"
                            name="dob"
                            data-label="DOB"
                            value="{{ $loan_officer -> dob }}">
                        </div>

                        <div class="m-2 sm:m-3">
                            <input
                            type="text"
                            class="form-element input md ssn required"
                            id="soc_sec"
                            name="soc_sec"
                            data-label="Social Security"
                            value="{{ \Crypt::decrypt($loan_officer -> soc_sec) }}">
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
                            value="{{ $loan_officer -> nmls_id }}">
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
                        id="loan_officer_docs"
                        name="loan_officer_docs"
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
                            <img class="rounded-lg shadow max-h-36" id="loan_officer_image" src="{{ $loan_officer -> photo_location_url }}"
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
                        <input type="file" id="loan_officer_photo" name="loan_officer_photo">

                        <x-modals.modal
                        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
                        :modalTitle="'Crop Photo'"
                        :modalId="'show_cropper_modal'"
                        :clickOutside="'loan_officer_photo_pond.removeFiles();'"
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

                <div class="text-xl font-medium text-gray-700 border-b mb-6 mt-12">Employee Bio</div>

                <div class="max-w-700-px">

                    <textarea class="form-element textarea md" id="bio" name="bio">{!! $loan_officer -> bio !!}</textarea>

                    <div class="flex justify-around items-center mt-4">

                        <button
                        type="button"
                        class="button primary md"
                        x-on:click="save_bio($el)">
                            <i class="fal fa-check mr-2"></i> Save Bio
                        </button>
                    </div>

                </div>

            </div>

        </div>

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
