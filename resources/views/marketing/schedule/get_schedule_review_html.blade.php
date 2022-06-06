@foreach ($events as $event)
    @php
        $accepted = null;
        $versions = [];
        if ($event -> uploads) {
            $event_upload = null;
            foreach ($event -> uploads as $upload) {
                $details = [
                    'file_id' => $upload -> id,
                    'file_type' => $upload -> file_type,
                    'file_url' => $upload -> file_url,
                    'html' => $upload -> html,
                ];
                if ($upload -> accepted_version == true) {
                    $accepted = $details;
                    $event_upload = $upload;
                }
                $versions[] = $details;
            }
        }
        
        $company = $event -> company -> item;
        
        $notes = [];
        if ($event -> notes) {
            $notes = $event -> notes;
            $count_unread = count($notes -> where('read', false) -> where('user_id', '!=', auth() -> user() -> id));
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
        data-company="{{ $company }}"
        data-medium-id="{{ $event -> medium_id }}"
        data-description="{{ $event -> description }}"
        data-subject-line-a="{{ $event -> subject_line_a }}"
        data-subject-line-b="{{ $event -> subject_line_b }}"
        data-preview-text="{{ $event -> preview_text }}"
        data-goal-id="{{ $event -> goal_id }}"
        data-focus-id="{{ $event -> focus_id }}">

        <div class="flex flex-col text-xs"
            x-data="{ show_edit_status: false, show_notes: false, show_add_notes: false }">

            <div class="relative flex justify-between items-center flex-wrap font-semibold bg-{{ $event -> company -> color }}-50 p-2 rounded-t"
                id="show_details_{{ $event -> id }}"
                @click="show_details = ! show_details; if(show_details === false) { show_notes = false }; hide_view_div();">

                <div class="flex flex-wrap justify-start items-center space-x-4 cursor-pointer text-{{ $event -> company -> color }}-700 @if ($event -> status -> item == 'Completed') opacity-40 @endif">
                    <div>
                        <button type="button"><i class="fa-light"
                                :class="show_details === false ? 'fa-bars' : 'fa-xmark fa-lg '"></i></button>
                    </div>
                    <div>
                        {{ $event -> event_date }}
                    </div>
                    <div class="w-32 hidden sm:inline-block">
                        {{ $event -> medium -> item }} - {{ $event -> id }}
                    </div>

                    <div class="bg-white px-2 py-1 rounded-lg border border-{{ $event -> company -> color }}-200 ">
                        {{ $event -> company -> item }} <i class="fa-light fa-arrow-right mx-2"></i> {{ $event -> recipient -> item }}
                    </div>

                </div>

                <div class="flex justify-end items-center">

                    <div class="relative">

                        <div class="rounded-lg p-1 text-white bg-{{ $event -> status -> color }}-600 cursor-pointer"
                            @click.stop="show_edit_status = ! show_edit_status">{{ $event -> status -> item }}</div>

                        <div class="origin-top-right absolute right-0 top-10 z-100 mt-2 w-200-px rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu"
                            aria-orientation="vertical"
                            aria-labelledby="menu-button"
                            tabindex="-1"
                            x-show="show_edit_status"
                            @click.outside="show_edit_status = false;">
                            <div class="p-4"
                                role="none">
                                @foreach ($settings -> where('category', 'status') as $status)
                                    <div class="group flex justify-between items-center p-2 rounded-lg cursor-pointer hover:bg-green-600/75 hover:text-white"
                                        @click.stop="if({{ $event -> status_id }} != {{ $status -> id }}) { update_status($el, {{ $event -> id }}, {{ $status -> id }}); } show_edit_status = false;">
                                        <div>{{ $status -> item }}</div>
                                        <div class="hidden group-hover:inline-block"><i class="fa-light fa-check"></i></div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                    </div>

                    <div class="mx-1 pl-4">
                        <div class="relative"
                            x-show="!show_notes">
                            <button type="button"
                                class="block w-full h-full"
                                @click.stop="get_notes({{ $event -> id }}, $refs.notes_div); show_notes = !show_notes">
                                <i class="fa-duotone fa-notes fa-2x text-{{ $event -> company -> color }}-700"></i>
                            </button>
                            <div class="absolute top-3 right-0 cursor-pointer flex items-center justify-around bg-orange-500 text-white p-1 rounded-full h-4 w-4 text-xxs notes-count @if ($count_unread == 0) hidden @endif"
                                data-note-id="{{ $event -> id }}"
                                @click.stop="get_notes({{ $event -> id }}, $refs.notes_div); show_notes = !show_notes">{{ $count_unread }}</div>
                        </div>

                        <div x-show="show_notes">
                            <button type="button"
                                class=""
                                @click="show_notes = false">
                                <i class="fa-duotone fa-times-circle text-red-600 hover:text-red-700 fa-2x"></i>
                            </button>
                        </div>

                    </div>

                </div>

            </div>



            <div class="p-4"
                x-show="show_notes"
                x-transition>

                <div class="w-full">

                    <div class="flex justify-end mb-2 max-w-700-px mx-auto">
                        <div>
                            <button type="button"
                                class="button success sm"
                                @click="show_add_notes = ! show_add_notes;"
                                x-show="show_add_notes === false">
                                <i class="fa-light fa-plus mr-2"></i> Add
                            </button>
                            <button type="button"
                                class=""
                                @click="show_add_notes = ! show_add_notes"
                                x-show="show_add_notes === true">
                                <i class="fa-duotone fa-times-circle text-red-600 hover:text-red-700 fa-2x"></i>
                            </button>
                        </div>
                    </div>

                    <div class="max-w-700-px mx-auto"
                        x-show="show_add_notes"
                        x-transition>
                        <form x-ref="add_notes_form">
                            <div>
                                <input class="editor-inline"
                                    name="notes"
                                    placeholder="Enter Notes"
                                    x-ref="notes">
                            </div>
                            <div class="flex justify-around my-3">
                                <button type="button"
                                    class="button primary md"
                                    @click.prevent="add_notes($el, {{ $event -> id }}); show_add_notes = false; $refs.notes.value = ''">
                                    Save Note <i class="fal fa-check ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="notes-div max-h-500-px overflow-auto pr-4 max-w-700-px mx-auto p-2 border rounded-lg shadow-md"
                        data-id="{{ $event -> id }}"></div>

                </div>

            </div>

            <div class=""
                x-show="show_details"
                x-transition>

                <div class="flex justify-start items-center p-2 border-b border-t">

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

                    <a href="javascript:void(0)"
                        class="text-primary hover:text-primary-light edit-button"
                        @click="edit_item($el); show_item_modal = true; add_event = false; edit_event = true;"
                        data-id="{{ $event -> id }}">
                        Edit <i class="fa-thin fa-edit ml-2"></i>
                    </a>

                    @if ($accepted)
                        <div class="mx-2 w-1 border-r"></div>
                        <a href="javascript:void(0)"
                            class="text-primary hover:text-primary-light"
                            @click="show_view_div('{{ $accepted['file_type'] }}', '{{ $accepted['file_url'] }}', `{{ $accepted['html'] }}`); ">View <i class="fa-thin fa-eye ml-2"></i></a>
                    @endif
                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)"
                        class="text-primary hover:text-primary-light"
                        @click="add_version({{ $event -> id }})">Add Version <i class="fa-thin fa-plus ml-2"></i></a>

                    <div class="mx-2 w-1 border-r"></div>

                    <div class="relative inline-block text-left"
                        x-data="{ show_links: false }"
                        @click.outside="show_links = false">
                        <div>
                            <a href="javascript:void(0)"
                                class="text-primary hover:text-primary-light"
                                @click="show_links = true">Links <i class="fa-thin fa-link ml-2"></i></a>
                        </div>

                        <div class="origin-top-left absolute -left-64 z-100 mt-2 w-600-px border-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu"
                            aria-orientation="vertical"
                            aria-labelledby="menu-button"
                            tabindex="-1"
                            x-show="show_links">
                            <div class="p-4"
                                role="none">

                                @php
                                    if ($company == 'Taylor Properties') {
                                        $links = [
                                            [
                                                'title' => 'Standard',
                                                'url' => 'https://taylorprops.com/careers?email={{ contact . EMAIL }}&utm_campaign=' . $event -> uuid,
                                            ],
                                            [
                                                'title' => 'Technology',
                                                'url' => 'https://taylorprops.com/careers#tech?email={{ contact . EMAIL }}&utm_campaign=' . $event -> uuid,
                                            ],
                                            [
                                                'title' => 'Join Now',
                                                'url' => 'https://taylorprops.com/careers#join?email={{ contact . EMAIL }}&utm_campaign=' . $event -> uuid,
                                            ],
                                        ];
                                    } elseif ($company == 'Heritage Title') {
                                        $links = [
                                            [
                                                'title' => 'Standard',
                                                'url' => 'https://heritagetitle.com?email=*|EMAIL|*&utm_campaign=' . $event -> uuid,
                                            ],
                                            [
                                                'title' => 'Instant Quote',
                                                'url' => 'https://heritagetitle.com/real-estate-title-and-escrow-services?email=*|EMAIL|*&utm_campaign=' . $event -> uuid,
                                            ],
                                        ];
                                    } elseif ($company == 'Heritage Financial') {
                                        $links = [
                                            [
                                                'title' => 'Loan Officer Jobs',
                                                'url' => 'https://heritagefinancial.com/jobs/loan-officer?email=[[contact.email]]&utm_campaign=' . $event -> uuid,
                                            ],
                                            [
                                                'title' => 'Agents',
                                                'url' => 'https://heritagefinancial.com/?email=[[contact.email]]&utm_campaign=' . $event -> uuid,
                                            ],
                                        ];
                                    }
                                @endphp

                                @foreach ($links as $link)
                                    <div class="flex justify-start items-center">

                                        <div class="w-36">
                                            {{ $link['title'] }}
                                        </div>
                                        <div class="flex p-0 border-2 rounded-md w-full">
                                            <div class="w-full">
                                                <input type="text"
                                                    readonly
                                                    class="w-full p-2"
                                                    x-ref="link_{{ $loop -> index }}"
                                                    value="{{ $link['url'] }}"
                                                    @focus="$el.select(); copy_text($el)">
                                            </div>
                                            <div class="w-8 border-l-2 bg-gray-50">
                                                <a href="javascript:void(0)"
                                                    class="block p-2"
                                                    @click="copy_text($refs.link_{{ $loop -> index }})"><i class="fa-duotone fa-clone"></i></a>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>

                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)"
                        class="text-primary hover:text-primary-light"
                        @click="get_email_list($el)">Get Email List <i class="fa-thin fa-download ml-2"></i></a>

                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)"
                        class="text-primary hover:text-primary-light"
                        role="menuitem"
                        @click="show_email($el, {{ $event -> id }}); show_dropdown = false;"><i class="fa-thin fa-envelope mr-2"></i> Email</a>

                    <div class="mx-2 w-1 border-r"></div>

                    <div class="relative inline-block"
                        x-data="{ show_dropdown: false }"
                        @click.outside="show_dropdown = false">
                        <div>
                            <button type="button"
                                class="block text-gray-400 hover:text-gray-600"
                                aria-expanded="true"
                                aria-haspopup="true"
                                @click="show_dropdown = true">
                                <span class="sr-only">Open options</span>
                                <i class="fa-light fa-bars fa-xl"></i>
                            </button>
                        </div>

                        <div class="origin-top-right absolute right-0 z-100 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu"
                            aria-orientation="vertical"
                            aria-labelledby="menu-button"
                            tabindex="-1"
                            x-show="show_dropdown">
                            <div class="py-2"
                                role="none">

                                <a href="javascript:void(0)"
                                    class="text-primary hover:text-primary-light hover:bg-gray-50 block px-4 py-2"
                                    role="menuitem"
                                    @click="clone({{ $event -> id }}); show_dropdown = false;"><i class="fa-thin fa-clone mr-2"></i> Clone</a>

                                <a href="javascript:void(0)"
                                    class="text-primary hover:text-primary-light hover:bg-gray-50 block px-4 py-2"
                                    role="menuitem"
                                    @click="show_versions({{ $event -> id }}); show_dropdown = false;"><span class="bg-blue-100 text-primary inline-flex items-center px-1.5 py-0.5 mr-2 rounded-full text-xs font-medium">{{ count($versions) }}</span> View Versions</a>

                                <hr>

                                <a href="javascript:void(0)"
                                    class="block px-4 py-2 text-red-600 hover:text-red-500"
                                    @click="show_delete_event({{ $event -> id }}, $el);  show_dropdown = false;"><i class="fa-duotone fa-trash mr-2"></i> Delete</a>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>



    </div>
@endforeach
