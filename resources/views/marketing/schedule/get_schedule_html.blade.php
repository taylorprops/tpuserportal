@foreach($events as $event)

    <div class="p-2 mb-2 text-sm rounded" id="event_{{ $event -> id }}">

        <div class="flex flex-col">

            <div class="flex justify-between font-semibold bg-blue-50 p-2 rounded-t">
                <div class="text-primary">
                    {{ $event -> event_date }}
                </div>
                <div class="text-gray-500">
                    {{ $event -> uuid }}
                </div>
                <div style="color: {{ $event -> company -> color }}">
                    {{ $event -> company -> item }}
                </div>
            </div>

            <div class="border-r border-l py-2">

                <div class="grid grid-cols-3 px-2">
                    <div>
                        {{ $event -> medium -> item }}
                    </div>
                    <div>
                        {{ str_replace(',', ', ', $event -> state) }}
                    </div>
                    <div class="text-right">
                        {{ $event -> recipient -> item }}
                    </div>
                </div>

                <div class="p-2 text-xs italic">
                    {{ $event -> description }}
                </div>

            </div>

            <div class="flex justify-around bg-gray-50 p-2 rounded-b">

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

@endforeach
