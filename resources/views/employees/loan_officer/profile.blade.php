@php
$title = 'Edit Profile';
$breadcrumbs = [
    ['Employees', ''],
    [$title],
];

$profile_link = '
<div class="flex justify-start items-center max-w-500-px bg-primary-lightest text-primary p-2 mb-12 rounded-lg w-auto border border-primary-light">
    <i class="fad fa-exclamation-circle fa-lg mr-4"></i>
    <div class="text-xs md:text-base">
        Your Profile Picture and Bio will appear on our public website - www.heritagefinancial.com
    </div>
    <div class="flex justify-end ml-4 whitespace-nowrap">
        <a class="text-xs md:text-base p-1 border flex items-center rounded-md bg-white shadow hover:bg-blue-50" href="https://heritagefinancial.com/about_us/view_loan_officer/'.$employee -> id .'" target="_blank">View Profile</a>
    </div>
</div>';
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-24 lg:pb-48 pt-10"
    x-data="profile('{{ $employee -> id }}', 'mortgage', @if($employee -> photo_location != '') true @else false @endif, ['#bio', '#signature'])">

        <div class="max-w-1000-px mx-auto px-4 sm:px-6 lg:px-12">

            <div class="sm:hidden">

                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full focus:ring-primary focus:border-primary border-gray-300 rounded-md"
                @change="active_tab = $el.value">
                    <option selected value="1">Details</option>
                    <option value="2">Photo</option>
                    <option value="3">Bio</option>
                    <option value="4">Signature</option>
                </select>

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


                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2', 'border-primary text-primary-dark': active_tab === '2' }"
                        @click="active_tab = '2'">
                            <i class="fad fa-portrait mr-3"
                            :class="{ 'text-primary': active_tab === '2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2' }"></i>
                            <span>Photo</span>
                        </a>

                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3', 'border-primary text-primary-dark': active_tab === '3' }"
                        @click="active_tab = '3'">
                            <i class="fad fa-copy mr-3"
                            :class="{ 'text-primary': active_tab === '3', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3' }"></i>
                            <span>Bio</span>
                        </a>

                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4', 'border-primary text-primary-dark': active_tab === '4' }"
                        @click="active_tab = '4'">
                            <i class="fad fa-signature mr-3"
                            :class="{ 'text-primary': active_tab === '4', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4' }"></i>
                            <span>Signature</span>
                        </a>

                    </nav>

                </div>

            </div>

            <div class="mt-8 md:mt-16">

                {{-- Contact/License Details --}}
                <div x-show="active_tab === '1'" x-transition">

                    <div class="mb-6 text-xl font-medium text-gray-700 border-b">Contact/License Details</div>

                    <div class="text-gray-500 mb-6">

                        <div class="text-yellow-600 italic mb-2">To edit any of these details, please contact the office.</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                            <div class="max-w-full sm:max-w-xs">
                                <div class="font-semibold text-xl">{{ $employee -> fullname }}</div>
                                {{ $employee -> phone }}<br>
                                {{ $employee -> email }}
                            </div>

                            <div class="max-w-full sm:max-w-xs">
                                <div class="font-semibold border-b">Home Address</div>
                                {{ $employee -> address_street }}<br>
                                {{ $employee -> address_city }}, {{ $employee -> address_state }}, {{ $employee -> address_zip }}
                            </div>

                            <div class="max-w-full sm:max-w-xs">
                                <div class="font-semibold border-b">NMLS ID</div>
                                {{ $employee -> nmls_id }}
                            </div>

                            <div class="max-w-full sm:max-w-xs">
                                <div class="font-semibold border-b">License(s)</div>
                                @foreach($employee -> licenses as $license)
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>{{ $license -> license_state }}</div>
                                        <div>{{ $license -> license_number }}</div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="mt-4">
                            <div class="font-semibold border-b">Profile Link</div>
                            <a href="www.heritagefinancial.com/{{ $employee -> folder }}" target="_blank">www.heritagefinancial.com/{{ $employee -> folder }}</a>
                        </div>

                    </div>

                </div>

                {{-- Profile Picture --}}
                <div x-show="active_tab === '2'" x-transition">

                    <div class="mb-6 text-xl font-medium text-gray-700 border-b">Your Profile Picture</div>

                    {!! $profile_link !!}

                    <div class="flex justify-start items-center max-w-500-px">

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

                {{-- Bio --}}
                <div x-show="active_tab === '3'" x-transition">

                    <div class="mb-6 text-xl font-medium text-gray-700 border-b">Your Bio</div>

                    {!! $profile_link !!}

                    <div class="max-w-700-px">

                        <textarea class="form-element textarea md" id="bio" name="bio">{!! $employee -> bio !!}</textarea>

                        <div class="flex justify-around items-center mt-4">
                            <button
                            type="button"
                            class="button primary xl px-8 py-6 text-lg"
                            x-on:click="save_bio($el)">
                                <i class="fal fa-check mr-2"></i> Save Bio
                            </button>
                        </div>

                    </div>

                </div>

                {{-- Signature --}}
                <div x-show="active_tab === '4'" x-transition">


                    <div class="mb-6 text-xl font-medium text-gray-700 border-b">Your Signature</div>

                    <div class="max-w-700-px">

                        <textarea class="form-element textarea md" id="signature" name="signature">{!! $employee -> signature !!}</textarea>

                        <div class="flex justify-around items-center mt-4">
                            <button
                            type="button"
                            class="button primary xl px-8 py-6 text-lg"
                            x-on:click="save_signature($el)">
                                <i class="fal fa-check mr-2"></i> Save Signature
                            </button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
