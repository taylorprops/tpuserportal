@foreach($events as $event)

    <div class=" mb-2 text-sm rounded border border-{{ $event -> company -> color }}-200" id="event_{{ $event -> id }}"
        x-data="{ show_details: false }">

        <div class="flex flex-col">

            <div class="flex justify-between items-center flex-wrap font-semibold bg-{{ $event -> company -> color }}-50 text-{{ $event -> company -> color }}-700 p-2 rounded-t"
                id="show_details_{{ $event -> id }}"
                @click="show_details = ! show_details">
                <div class="flex justify-start space-x-6 cursor-pointer">
                    <div>
                        <button type="button"><i class="fa-light" :class="show_details === false ? 'fa-bars' : 'fa-xmark fa-lg '"></i></button>
                    </div>
                    <div>
                        {{ $event -> event_date }}
                    </div>
                    <div class="w-40">
                        {{ $event -> medium -> item }}
                    </div>
                    <div class="font-semibold">
                        {{ $event -> uuid }}
                    </div>
                </div>
                <div class="bg-white px-2 py-1 rounded-lg border border-{{ $event -> company -> color }}-200">
                    {{ $event -> company -> item }} <i class="fa-light fa-arrow-right mx-2"></i> {{ $event -> recipient -> item }}
                </div>
            </div>

            <div class="py-2" x-show="show_details">

                <div class="flex justify-start items-center p-2">

                    <div class="pr-4 border-r">
                        {{ str_replace(',', ', ', $event -> state) }}
                    </div>

                    <div class="p-2 pl-4 text-xs italic">
                        {{ $event -> description }}
                    </div>

                </div>

                <div class="flex justify-around border-t p-2 pb-0">

                    @php
                    $accepted = null;
                    $versions = [];
                    foreach($event -> uploads as $upload) {
                        $details = [
                            'file_id' => $upload -> id,
                            'file_type' => $upload -> file_type,
                            'file_url' => $upload -> file_url,
                            'html' => $upload -> html,
                        ];
                        if($upload -> accepted_version == true) {
                            $accepted = $details;
                        }
                        $versions[] = $details;
                    }
                    @endphp

                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light edit-button"
                    data-id="{{ $event -> id }}"
                    data-event-date="{{ $event -> event_date }}"
                    data-state="{{ $event -> state }}"
                    data-recipient-id="{{ $event -> recipient_id }}"
                    data-company-id="{{ $event -> company_id }}"
                    data-medium-id="{{ $event -> medium_id }}"
                    data-description="{{ $event -> description }}"
                    data-subject-line-a="{{ $event -> subject_line_a }}"
                    data-subject-line-b="{{ $event -> subject_line_b }}"
                    data-preview-text="{{ $event -> preview_text }}"
                    @click="edit_item($el); show_item_modal = true; add_event = false; edit_event = true;">
                        Edit <i class="fa-thin fa-edit ml-2"></i>
                    </a>

                    <div class="mx-2 w-1 border-r"></div>
                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_view_div('{{ $accepted['file_type'] }}', '{{ $accepted['file_url'] }}', `{{ $accepted['html'] }}`); ">View <i class="fa-thin fa-eye ml-2"></i></a>

                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="add_version({{ $event -> id }})">Add Version <i class="fa-thin fa-plus ml-2"></i></a>

                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_versions({{ $event -> id }})">View Versions <span class="bg-blue-100 text-primary inline-flex items-center px-1.5 py-0.5 ml-2 rounded-full text-xs font-medium">{{ count($versions) }}</span></a>

                    <div class="mx-2 w-1 border-r"></div>

                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="clone({{ $event -> id }}, $el)">Clone <i class="fa-thin fa-clone ml-2"></i></a>

                </div>

            </div>

        </div>



    </div>

@endforeach
