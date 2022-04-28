@php
$title = 'Marketing Schedule';
$breadcrumbs = [
['Marketing', ''],
[$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2 overflow-x-hidden" x-data="schedule()">

        <div class="w-full mx-12 sm:px-6 lg:px-12">

            <div class="my-6">
                <button type="button" class="button primary lg" @click="show_add_item_modal = true">
                    Add Item <i class="fa-light fa-plus ml-3"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="">

                    <div class="border rounded-lg p-4 h-screen-80 overflow-auto">

                        <div x-ref="schedule_list_div"></div>


                    </div>

                </div>

                <div class="col-span-2 flex flex-col">

                    <div class="relative mr-8 h-screen-80 overflow-auto">

                        <div class="absolute top-0 bg-white rounded border p-4 z-100 w-full h-full" x-show="show_html" x-ref="view_html"></div>

                        <div class="absolute top-0 bg-white rounded border p-4 z-100 w-full h-full" x-show="show_file">
                            <embed src="" type="application/pdf" class="min-h-750-px" width="100%" height="100vh" x-ref="view_file" />
                        </div>

                        <div class="z-10">
                            Calendar
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <x-modals.modal :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/2'" :modalTitle="'Add Marketing Item'" :modalId="'show_add_item_modal'" x-show="show_add_item_modal">

            <form x-ref="schedule_form" enctype="multipart/form-data">

                <div class="p-2 sm:p-4 lg:p-8">

                    <div class="text-lg font-semibold my-4">Details</div>

                    <div class="grid grid-cols-1 md:grid-cols-9 gap-8">

                        <div class="col-span-2">
                            <input type="date" class="form-element input md required" name="event_date" data-label="Event Date">
                        </div>

                        <div class="col-span-3">
                            <select class="form-element select md required" name="recipient_id" data-label="Recipient">
                                <option value=""></option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient -> id }}">{{ $recipient -> recipient }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-4">

                            <div class="text-gray-500 text-sm">States</div>

                            <div class="flex justify-between shadow rounded-md">

                                @foreach($states as $state)

                                <label for="{{ $state }}" class="@if($loop -> first) rounded-l-md border-r border-gray-200 @elseif ($loop -> last) rounded-r-md @else border-r border-gray-200 @endif
                                    flex justify-around items-center py-2 w-full  cursor-pointer" x-data="{ active: false }" x-ref="{{ $state }}" :class="active === true ? 'bg-primary text-white' : 'color-gray-700 hover:bg-gray-50'">
                                    {{ $state }}
                                    <input type="checkbox" class="hidden states @if ($loop -> last) form-element required @endif" name="state[]" id="{{ $state }}" value="{{ $state }}" @change="active = $el.checked; $refs.states.value = document.querySelectorAll('.states:checked').length">
                                </label>

                                @endforeach

                            </div>

                            <input type="hidden" name="states" x-ref="states" value="0">

                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mt-8">

                        <div class="">
                            <select class="form-element select md required" name="company_id" data-label="Company">
                                <option value=""></option>
                                @foreach($companies as $company)
                                <option value="{{ $company -> id }}">{{ $company -> company }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="">
                            <select class="form-element select md required" name="medium_id" data-label="Medium">
                                <option value=""></option>
                                @foreach($mediums as $medium)
                                <option value="{{ $medium -> id }}">{{ $medium -> medium }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2">
                            <input type="text" class="form-element input md required" name="description" data-label="Description">
                        </div>

                    </div>

                    <div>

                        <div class="text-lg font-semibold mt-12 mb-4">Upload</div>

                        <div x-data="{ active_tab: 'html' }">

                            <div class="sm:hidden">
                                <label for="tabs" class="sr-only">Select an Option</label>
                                <select id="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                                @change="active_tab = $el.value;">
                                    <option value="html">Paste HTML</option>
                                    <option value="file">Image/PDF</option>
                                </select>
                            </div>

                            <div class="hidden sm:block">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                        :class="active_tab === 'html' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';"
                                        @click="active_tab = 'html'"> Paste HTML </a>

                                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page"
                                        @click="active_tab = 'file'"
                                        :class="active_tab === 'file' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';"> PDF/Image </a>

                                    </nav>
                                </div>
                            </div>

                            <div class="mt-8">

                                <div x-show="active_tab === 'html'" x-transition>
                                    <textarea class="form-element textarea md" rows="8" name="upload_html"
                                    x-ref="upload_html"
                                    @change="$refs.upload_file.value = ''; show_file_names($refs.upload_file)"></textarea>
                                </div>

                                <div x-show="active_tab === 'file'" x-transition>
                                    <div>
                                        <input type="file" name="upload_file" class="form-element input md" @change="show_file_names($el);" accept="image/x-png,image/gif,image/jpeg,application/pdf"
                                        x-ref="upload_file"
                                        @click="$refs.upload_html.value = ''">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="flex justify-around items-center pb-6 pt-12">
                        <button type="button" class="button primary xl" @click="save_add_item($el)">Save Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                </div>

            </form>

        </x-modals.modal>

    </div>

</x-app-layout>