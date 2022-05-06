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

    <div class="pb-12 pt-2 overflow-hidden" x-data="schedule()">

        <div class="mx-4">

            <form x-ref="filter_form">

                <div class="my-6 flex justify-start items-end space-x-4">

                    <div>
                        <button type="button" class="button primary lg" @click="show_item_modal = true; add_event = true; edit_event = false; $refs.id.value = ''; clear_form($refs.schedule_form)">
                            Add Item <i class="fa-light fa-plus ml-3"></i>
                        </button>
                    </div>

                    <div>
                        <select class="form-element select md" data-label="Company" name="company_id" @change="get_schedule()">
                            <option value="">All</option>
                            @foreach($settings -> where('category', 'company') as $company)
                            <option value="{{ $company -> id }}">{{ $company -> item }}
                                @endforeach
                        </select>
                    </div>

                    <div>
                        <select class="form-element select md" data-label="Recipients" name="recipient_id" @change="get_schedule()">
                            <option value="">All</option>
                            @foreach($settings -> where('category', 'recipient') as $recipient)
                            <option value="{{ $recipient -> id }}">{{ $recipient -> item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select class="form-element select md" data-label="Medium" name="medium_id" @change="get_schedule()">
                            <option value="">All</option>
                            @foreach($settings -> where('category', 'medium') as $medium)
                            <option value="{{ $medium -> id }}">{{ $medium -> item }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </form>

            <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-8">

                <div>

                    <div class="border rounded-lg p-2 max-h-screen-80 overflow-auto mb-12 lg:mb-0">

                        <div x-ref="schedule_list_div"></div>


                    </div>

                </div>

                <div class="flex flex-col">

                    <div class="relative lg:h-screen-80 lg:overflow-auto">

                        <div x-show="show_html || show_file">

                            <div class="absolute top-12 right-12 z-20"><a href="javascript:void(0)" @click="show_html = false; show_file = false; show_calendar = true;"><i class="fa-duotone fa-circle-xmark fa-3x text-red-600 hover:text-red-500"></i></a></div>

                            <div class="absolute top-0 bg-white rounded border p-4 z-10 w-full h-full" x-show="show_html" x-ref="view_html">
                                <iframe class="view-accepted-iframe" width="100%" height="100%"></iframe>
                            </div>

                            <div class="absolute top-0 bg-white rounded border p-4 z-10 w-full h-full" x-show="show_file">
                                <embed src="" type="application/pdf" class="min-h-750-px" width="100%" height="100vh" x-ref="view_file" />
                            </div>

                        </div>

                        <div class="z-10" x-show="show_calendar">
                            <div class="calendar"></div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <x-modals.modal :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/2'" :modalTitle="'Add/Edit Marketing Item'" :modalId="'show_item_modal'" x-show="show_item_modal" :clickOutside="'show_item_modal = true;'">

            <form x-ref="schedule_form" enctype="multipart/form-data">

                <div class="p-2 sm:p-4 lg:p-8 lg:pt-0">

                    <div class="text-lg font-semibold my-4">Details</div>

                    <div class="grid grid-cols-1 md:grid-cols-9 gap-4">

                        <div class="col-span-2">
                            <input type="date" class="form-element input md required" name="event_date" x-ref="event_date" data-label="Event Date">
                        </div>

                        <div class="col-span-3">
                            <select class="form-element select md required" name="recipient_id" x-ref="recipient_id" data-label="Recipient"
                            @change="
                            if($el.options[$el.selectedIndex].text.match(/In\sHouse\sAgents/)) {
                                document.querySelectorAll('.states').forEach(function(state) {
                                    state.click();
                                })
                            }">
                                <option value=""></option>
                                @foreach($settings -> where('category', 'recipient') as $recipient)
                                <option value="{{ $recipient -> id }}">{{ $recipient -> item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-4">

                            <div class="text-gray-500 text-sm">States</div>

                            <div class="flex justify-between shadow rounded-md">

                                @foreach($states as $state)

                                    <label for="{{ $state }}"
                                    class="@if($loop -> first) rounded-l-md border-r border-gray-200 @elseif ($loop -> last) rounded-r-md @else border-r border-gray-200 @endif
                                    flex justify-around items-center py-2 w-full cursor-pointer state-radios"
                                    x-data="{ active: false }"
                                    x-ref="{{ $state }}"
                                    :class="active === true ? 'bg-primary text-white' : 'color-gray-700 hover:bg-gray-50'">
                                        {{ $state }}
                                        <input type="checkbox" class="hidden states @if ($loop -> last) form-element required @endif" name="state[]" id="{{ $state }}" value="{{ $state }}" @change="active = $el.checked; $refs.states.value = document.querySelectorAll('.states:checked').length">
                                    </label>

                                @endforeach

                            </div>

                            <input type="hidden" name="states" x-ref="states" value="0">

                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">

                        <div class="">
                            <select class="form-element select md required" name="company_id" x-ref="company_id" data-label="Company">
                                <option value=""></option>
                                @foreach($settings -> where('category', 'company') as $company)
                                <option value="{{ $company -> id }}">{{ $company -> item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="">
                            <select class="form-element select md required" name="medium_id" x-ref="medium_id" data-label="Medium" @change="$el.options[$el.selectedIndex].text == 'Email' ? show_email_options = true : show_email_options = false">
                                <option value=""></option>
                                @foreach($settings -> where('category', 'medium') as $medium)
                                <option value="{{ $medium -> id }}">{{ $medium -> item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2">
                            <input type="text" class="form-element input md required" name="description" x-ref="description" data-label="Description">
                        </div>

                        <div class="">
                            <select class="form-element select md" name="focus_id" x-ref="focus_id" data-label="Focus">
                                <option value=""></option>
                                @foreach($settings -> where('category', 'focus') as $focus)
                                <option value="{{ $focus -> id }}">{{ $focus -> item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="">
                            <select class="form-element select md" name="goal_id" x-ref="goal_id" data-label="Goal">
                                <option value=""></option>
                                @foreach($settings -> where('category', 'goal') as $goal)
                                <option value="{{ $goal -> id }}">{{ $goal -> item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2" x-show="show_email_options">
                            <input type="text" class="form-element input md" name="subject_line_a" x-ref="subject_line_a" data-label="Subject Line A">
                        </div>

                        <div class="col-span-2" x-show="show_email_options">
                            <input type="text" class="form-element input md" name="subject_line_b" x-ref="subject_line_b" data-label="Subject Line B">
                        </div>

                        <div class="col-span-4" x-show="show_email_options">
                            <input type="text" class="form-element input md" name="preview_text" x-ref="preview_text" data-label="Preview Text">
                        </div>

                    </div>

                    <div x-show="add_event">

                        <div class="text-lg font-semibold mt-8">Add Content </div>

                        <div x-data="{ active_tab: 'html' }">

                            <div class="sm:hidden">
                                <label for="tabs" class="sr-only">Select an Option</label>
                                <select id="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md" @change="active_tab = $el.value;">
                                    <option value="html">Paste HTML</option>
                                    <option value="file">Image/PDF</option>
                                </select>
                            </div>

                            <div class="hidden sm:block">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" :class="active_tab === 'html' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';" @click="active_tab = 'html'"> HTML </a>

                                        <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page" @click="active_tab = 'file'" :class="active_tab === 'file' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';"> PDF/Image </a>

                                    </nav>
                                </div>
                            </div>

                            <div class="mt-8">

                                <div x-show="active_tab === 'html'" x-transition>
                                    <div class="flex w-full items-end">
                                        <div class="w-4/5">
                                            <input type="text" class="form-element input md" data-label="Paste URL" x-ref="paste_link" @change="get_html_from_link($el, $refs.upload_html);">
                                        </div>
                                        <div class="ml-2">
                                            <button type="button" class="button primary md">Get HTML <i class="fa-light fa-arrow-right ml-2"></i></button>
                                        </div>
                                    </div>
                                    <div class="my-2">OR</div>
                                    <div>
                                        <textarea class="form-element textarea md" rows="3" name="upload_html" data-label="Paste HTML" x-ref="upload_html" @change="$refs.upload_file.value = ''; show_file_names($refs.upload_file)"></textarea>
                                    </div>
                                </div>

                                <div x-show="active_tab === 'file'" x-transition>
                                    <div>
                                        <input type="file" name="upload_file" class="form-element input md" @change="show_file_names($el);" accept="image/x-png,image/gif,image/jpeg,application/pdf" x-ref="upload_file" @click="$refs.upload_html.value = ''">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="flex justify-around items-center">

                        <div class="mt-4 flex justify-around" x-show="edit_event">
                            <a href="javascript:void(0)" class="text-red-600 hover:text-red-500" @click="show_delete_event($el.getAttribute('data-id'), $el)" x-ref="delete_event_button">Delete <i class="fa-duotone fa-trash ml-2"></i></a>
                        </div>

                        <div class="flex justify-around items-center pb-6 pt-12">
                            <button type="button" class="button primary xl" @click="save_item($el)">Save Item <i class="fa-light fa-check ml-2"></i></button>
                        </div>

                        <div class="mt-4 flex justify-around" x-show="edit_event">
                            <a href="javascript:void(0)" class="text-primary hover:text-primary-light" x-ref="show_versions_button">Show Versions <i class="fa-duotone fa-clone ml-2"></i></a>
                        </div>

                    </div>



                </div>

                <input type="hidden" name="id" x-ref="id">

            </form>

        </x-modals.modal>

        <x-modals.modal :modalWidth="'w-screen-95'" :modalTitle="''" :modalId="'show_versions_modal'" x-show="show_versions_modal" :clickOutside="'show_versions_modal = true;'">
            <div x-ref="versions_div"></div>
        </x-modals.modal>

        <x-modals.modal :modalWidth="'w-full sm:w-3/4 md:w-1/3 lg:w-1/4'" :modalTitle="''" :modalId="'show_delete_event_modal'" x-show="show_delete_event_modal" :clickOutside="'show_delete_event_modal = true;'">
            <div>
                Are you sure you want to send this event to the recycle bin?
            </div>
            <div class="flex justify-around items-center py-6">
                <button type="button" class="button danger sm"
                @click="show_delete_event_modal = false">
                    Cancel <i class="fa-light fa-xmark ml-2"></i>
                </button>
                <button type="button" class="button primary md"
                x-ref="delete_event">
                    Confirm <i class="fa-light fa-check ml-2"></i>
                </button>
            </div>
        </x-modals.modal>

        <x-modals.modal :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/2'" :modalTitle="'Add New Version'" :modalId="'show_add_version_modal'" x-show="show_add_version_modal">

            <div x-data="{ active_tab: 'html' }">

                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Select an Option</label>
                    <select id="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md" @change="active_tab = $el.value; clear_form($refs.add_version_form); clear_add_version_form()">
                        <option value="html">Paste HTML</option>
                        <option value="file">Image/PDF</option>
                    </select>
                </div>

                <div class="hidden sm:block">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                            <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="active_tab === 'html' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';"
                            @click="active_tab = 'html'; clear_add_version_form()"> Paste HTML </a>

                            <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page"
                            @click="active_tab = 'file'; clear_add_version_form()"
                            :class="active_tab === 'file' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';"> PDF/Image </a>

                        </nav>
                    </div>
                </div>

                <form id="add_version_form">

                    <div class="mt-8">

                        <div x-show="active_tab === 'html'" x-transition>
                            <div>
                                <input type="text" class="form-element input md"
                                data-label="Paste URL"
                                @change="get_html_from_link($el, $refs.upload_version_html);"
                                @paste="get_html_from_link($el, $refs.upload_version_html);">
                            </div>
                            <div class="my-2">OR</div>
                            <div>
                                <textarea class="form-element textarea md" rows="3" name="upload_version_html"
                                data-label="Paste HTML"
                                x-ref="upload_version_html"></textarea>
                            </div>
                        </div>

                        <div x-show="active_tab === 'file'" x-transition>
                            <div class="mt-12">
                                <input type="file" name="upload_version_file" class="form-element input md"
                                @change="show_file_names($el)"
                                x-ref="upload_version_file"
                                accept="image/x-png,image/gif,image/jpeg,application/pdf">
                            </div>
                        </div>

                    </div>

                    <input type="hidden" name="event_id" id="event_id">

                </form>

            </div>

            <div class="flex justify-around items-center pb-6 pt-12">
                <button type="button" class="button primary xl" @click="save_add_version($el, true)">Save Version <i class="fa-light fa-check ml-2"></i></button>
            </div>

        </x-modals.modal>

    </div>

</x-app-layout>
