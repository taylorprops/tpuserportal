{{-- blade-formatter-disable --}}
@php
    $title = 'Upload List';
    $breadcrumbs = [
    ['Marketing', ''],
    [$title],
    ];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="upload_list()">

        <div class="max-w-1000-px mx-auto sm:px-6 lg:px-12 pt-12">

            <div class="text-lg font-semibold text-gray-500 mb-8">Add New List</div>

            <div class="">

                <form id="add_list_form" enctype="multipart/form-data">

                    <div class="w-48 mb-8">
                        <select class="form-element select md" name="list_type" data-label="Select List Type"
                        @change="list_type = $el.value">
                            <option value="in_house">In House Agents</option>
                            <option value="test_center">Test Center Agents</option>
                        </select>
                    </div>

                    <div class="text-sm text-gray-400 mb-8">
                    <div x-show="list_type === 'in_house'">
                        Upload excel or csv<br>
                        Include header row<br>
                        Columns: First Name, Last Name, Email, Cell Phone, Street, City, State, Zip, Company, Start Date, MLS ID 1, MLS ID 2, MLS ID 3
                    </div>
                    <div x-show="list_type === 'test_center'">
                        Upload excel or csv<br>
                        Include header row<br>
                        Columns: Name, Address1, Address2, City, State, Zip, Phone, Email, LastTestDate, TestName, Result

                    </div>
                </div>

                    <div class="mb-8" x-show="list_type === 'test_center'">

                        <div class="text-gray-600 text-sm w-full">Select State</div>

                        <div class="flex justify-start">

                            @foreach($states_test_center as $state)

                            <div class="@if(!$loop -> first) ml-6 @endif">
                                <input type="radio" class="states form-element radio primary lg states" name="state" id="{{ $state }}" value="{{ $state }}" data-label="{{ $state }}">
                            </div>

                            @endforeach

                        </div>
                    </div>

                    <div class="mb-8">
                        <input type="file" class="form-element input md" name="upload_input" id="upload_input" x-on:change="show_file_names($el, false);" x-ref="upload_input">
                    </div>

                    <div class="">
                        <button class="button primary lg" @click.prevent="add_list($el)">
                            Upload List <i class="fa-light fa-check ml-2"></i>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
