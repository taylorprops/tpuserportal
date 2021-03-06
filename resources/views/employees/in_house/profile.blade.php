{{-- blade-formatter-disable --}}
@php
$title = 'Edit Profile';
$breadcrumbs = [
    ['Employees', ''],
    [$title],
];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-24 lg:pb-48 pt-2"
    x-data="profile('{{ $employee -> id }}', 'in_house', @if($employee -> photo_location != '') true @else false @endif, '#signature')">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-12">

            <div class="pb-3 mb-3 mt-8 md:mt-16 text-xl font-medium text-gray-700 border-b">Contact/License Details</div>

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


                </div>

            </div>

            <div class="p-2 lg:p-8 rounded-lg border mt-12 lg:mt-16">


                <div class="text-xl font-medium text-gray-700 border-b mb-6 mt-4">Your Profile Picture</div>

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


                <div class="text-xl font-medium text-gray-700 border-b mb-6 mt-12">Your Signature</div>

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

</x-app-layout>
