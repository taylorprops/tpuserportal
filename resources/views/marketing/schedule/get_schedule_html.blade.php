@foreach($events as $event)

@php
$accepted = null;
$versions = [];
if($event -> uploads) {
    $event_upload = null;
    foreach($event -> uploads as $upload) {
        $details = [
            'file_id' => $upload -> id,
            'file_type' => $upload -> file_type,
            'file_url' => $upload -> file_url,
            'html' => $upload -> html,
        ];
        if($upload -> accepted_version == true) {
            $accepted = $details;
            $event_upload = $upload;
        }
        $versions[] = $details;
    }
}
@endphp

<div class="event-div my-2 text-sm rounded border-{{ $event -> company -> color }}-200"
    :class="show_details === true ? 'border-4 shadow-lg my-4' : 'border'"
    id="event_{{ $event -> id }}"
    x-data="{ show_details: false }"
    data-id="{{ $event -> id }}"
    data-event-date="{{ $event -> event_date }}"
    data-state="{{ $event -> state }}"
    data-status-id="{{ $event -> status_id }}"
    data-recipient-id="{{ $event -> recipient_id }}"
    data-recipient="{{ $event -> recipient -> item }}"
    data-company-id="{{ $event -> company_id }}"
    data-company="{{ $event -> company -> item }}"
    data-medium-id="{{ $event -> medium_id }}"
    data-description="{{ $event -> description }}"
    data-subject-line-a="{{ $event -> subject_line_a }}"
    data-subject-line-b="{{ $event -> subject_line_b }}"
    data-preview-text="{{ $event -> preview_text }}"
    data-goal-id="{{ $event -> goal_id }}"
    data-focus-id="{{ $event -> focus_id }}">

    <div class="flex flex-col">

        <div class="relative flex justify-between items-center flex-wrap font-semibold bg-{{ $event -> company -> color }}-50 p-2 rounded-t" id="show_details_{{ $event -> id }}" @click="show_details = ! show_details"
        x-data="{ show_edit_status: false }">
            <div class="flex flex-wrap justify-start items-center space-x-6 cursor-pointer text-{{ $event -> company -> color }}-700 @if($event -> status -> item == 'Completed') opacity-40 @endif">
                <div>
                    <button type="button"><i class="fa-light" :class="show_details === false ? 'fa-bars' : 'fa-xmark fa-lg '"></i></button>
                </div>
                <div>
                    {{ $event -> event_date }}
                </div>
                <div class="w-40 hidden sm:inline-block">
                    {{ $event -> medium -> item }} @if($event_upload && $event_upload -> html != '') - {{ $event -> id }} @endif
                </div>

                <div class="bg-white px-2 py-1 rounded-lg border border-{{ $event -> company -> color }}-200 ">
                    {{ $event -> company -> item }} <i class="fa-light fa-arrow-right mx-2"></i> {{ $event -> recipient -> item }}
                </div>

            </div>

            <div class="rounded-lg p-1 text-white bg-{{ $event -> status -> color }}-600 cursor-pointer" @click.stop="show_edit_status = true">{{ $event -> status -> item }}</div>

            <div class="origin-top-right absolute right-0 top-10 z-100 mt-2 w-200-px rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
            x-show="show_edit_status"
            @click.outside="show_edit_status = false;">
                    <div class="p-4" role="none">
                        @foreach($settings -> where('category', 'status') as $status)
                            <div class="group flex justify-between items-center p-2 rounded-lg cursor-pointer hover:bg-green-600/75 hover:text-white"
                            @click.stop="update_status($el, {{ $event -> id }}, {{ $status -> id }}); show_edit_status = false;">
                                <div>{{ $status -> item }}</div>
                                <div class="hidden group-hover:inline-block"><i class="fa-light fa-check"></i></div>
                            </div>
                        @endforeach
                    </div>

            </div>

        </div>

        <div class="" x-show="show_details">

            <div class="flex justify-start items-center p-2 border-b">

                <div class="pr-4 border-r">
                    {{ str_replace(',', ', ', $event -> state) }}
                </div>

                <div class="pl-4 italic">
                    {{ $event -> description }}
                </div>

            </div>

            <div class="flex justify-start items-center px-2 py-1 text-gray-500">

                <div class="pr-4 border-r w-28 text-right">
                    Subject A
                </div>

                <div class="pl-4">
                    {{ $event -> subject_line_a }}
                </div>

            </div>

            <div class="flex justify-start items-center px-2 py-1 text-gray-500">

                <div class="pr-4 border-r w-28 text-right">
                    Subject B
                </div>

                <div class="pl-4">
                    {{ $event -> subject_line_b }}
                </div>

            </div>

            <div class="flex justify-start items-center px-2 pt-1 pb-2 text-gray-500">

                <div class="pr-4 border-r w-28 text-right">
                    Preview Text
                </div>

                <div class="pl-4">
                    {{ $event -> preview_text }}
                </div>

            </div>

            <div class="flex justify-around flex-wrap whitespace-nowrap border-t p-2 bg-{{ $event -> company -> color }}-50">

                <a href="javascript:void(0)" class="text-primary hover:text-primary-light edit-button"
                @click="edit_item($el); show_item_modal = true; add_event = false; edit_event = true;">
                    Edit <i class="fa-thin fa-edit ml-2"></i>
                </a>

                @if($accepted)
                <div class="mx-2 w-1 border-r"></div>
                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_view_div('{{ $accepted['file_type'] }}', '{{ $accepted['file_url'] }}', `{{ $accepted['html'] }}`); ">View <i class="fa-thin fa-eye ml-2"></i></a>
                @endif
                <div class="mx-2 w-1 border-r"></div>

                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="add_version({{ $event -> id }})">Add Version <i class="fa-thin fa-plus ml-2"></i></a>

                <div class="mx-2 w-1 border-r"></div>

                <div class="relative inline-block text-left" x-data="{ show_links: false }"
                @click.outside="show_links = false">
                    <div>
                        <a href="javascript:void(0)" class="text-primary hover:text-primary-light"
                        @click="show_links = true">Links <i class="fa-thin fa-link ml-2"></i></a>
                    </div>

                    <div class="origin-top-right absolute right-0 z-100 mt-2 w-400-px rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" x-show="show_links">
                        <div class="p-4" role="none">

                            <div class="flex justify-start items-center">

                                <div class="w-24">
                                    Standard
                                </div>
                                <div class="flex p-0 border-2 rounded-md w-full">
                                    <div class="w-full">
                                        <input type="text" readonly class="w-full p-2" x-ref="standard" value="https://taylorprops.com/careers?email=@{{contact.EMAIL}}&utm_campaign={{ $event -> uuid }}">
                                    </div>
                                    <div class="w-8 border-l-2 bg-gray-50">
                                        <a href="javascript:void(0)" class="block p-2" @click="copy_text($refs.standard)"><i class="fa-duotone fa-clone"></i></a>
                                    </div>
                                </div>

                            </div>

                            <div class="flex justify-start items-center">

                                <div class="w-24">
                                    Technology
                                </div>
                                <div class="flex p-0 border-2 rounded-md w-full">
                                    <div class="w-full">
                                        <input type="text" readonly class="w-full p-2" x-ref="tech" value="https://taylorprops.com/careers#tech?email=@{{contact.EMAIL}}&utm_campaign={{ $event -> uuid }}">
                                    </div>
                                    <div class="w-8 border-l-2 bg-gray-50">
                                        <a href="javascript:void(0)" class="block p-2" @click="copy_text($refs.tech)"><i class="fa-duotone fa-clone"></i></a>
                                    </div>
                                </div>

                            </div>

                            <div class="flex justify-start items-center">

                                <div class="w-24">
                                    Join Now
                                </div>
                                <div class="flex p-0 border-2 rounded-md w-full">
                                    <div class="w-full">
                                        <input type="text" readonly class="w-full p-2" x-ref="join" value="https://taylorprops.com/careers#join?email=@{{contact.EMAIL}}&utm_campaign={{ $event -> uuid }}">
                                    </div>
                                    <div class="w-8 border-l-2 bg-gray-50">
                                        <a href="javascript:void(0)" class="block p-2" @click="copy_text($refs.join)"><i class="fa-duotone fa-clone"></i></a>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>

                <div class="mx-2 w-1 border-r"></div>

                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="get_email_list($el)">Get Email List <i class="fa-thin fa-download ml-2"></i></a>

                <div class="mx-2 w-1 border-r"></div>

                <div class="relative inline-block" x-data="{ show_dropdown: false }"
                @click.outside="show_dropdown = false">
                    <div>
                        <button type="button" class="block text-gray-400 hover:text-gray-600" aria-expanded="true" aria-haspopup="true"
                        @click="show_dropdown = true">
                            <span class="sr-only">Open options</span>
                            <i class="fa-light fa-bars fa-xl"></i>
                        </button>
                    </div>

                    <div class="origin-top-right absolute right-0 z-100 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" x-show="show_dropdown">
                        <div class="py-2" role="none">

                            <a href="javascript:void(0)" class="text-primary hover:text-primary-light hover:bg-gray-50 block px-4 py-2" role="menuitem"
                            @click="clone({{ $event -> id }}); show_dropdown = false;"><i class="fa-thin fa-clone mr-2"></i> Clone</a>

                            <a href="javascript:void(0)" class="text-primary hover:text-primary-light hover:bg-gray-50 block px-4 py-2" role="menuitem"
                            @click="show_email($el, {{ $event -> id }}); show_dropdown = false;"><i class="fa-thin fa-envelope mr-2"></i> Email</a>

                            <a href="javascript:void(0)" class="text-primary hover:text-primary-light hover:bg-gray-50 block px-4 py-2" role="menuitem"
                            @click="show_versions({{ $event -> id }}); show_dropdown = false;"><span class="bg-blue-100 text-primary inline-flex items-center px-1.5 py-0.5 mr-2 rounded-full text-xs font-medium">{{ count($versions) }}</span> View Versions</a>

                            <hr>

                            <a href="javascript:void(0)" class="block px-4 py-2 text-red-600 hover:text-red-500" @click="show_delete_event({{ $event -> id }}, $el);  show_dropdown = false;"><i class="fa-duotone fa-trash mr-2"></i> Delete</a>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>



</div>

@endforeach
