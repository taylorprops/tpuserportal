

<ul class="form-ul pb-20 animate__animated animate__fadeIn" x-data="{ active_form: '' }">

    @foreach($forms as $form)

    @php
    $form_id = $form -> id;
    $form_name_display = $form -> form_name_display;
    $form_location = $form -> form_location;
    $form_group_id = $form -> form_group_id;
    $checklist_group_id = $form -> checklist_group_id;
    $form_tag = $form -> form_tag;
    $state = $form -> state;
    $helper_text = $form -> helper_text;
    $created_at = $form -> created_at;
    $fields_count = count($form -> fields);
    @endphp

        <li class="form-{{ $form_id }} p-3 w-full"
        :class="{ 'bg-secondary-lightest rounded' : active_form === '{{ $form_id }}', 'border-b' : active_form !== '{{ $form_id }}' }">

            <div class="flex justify-between items-center">
                <div>
                    <a href="/storage/{{ $form -> form_location }}" target="_blank">{{ $form -> form_name_display }}</a>
                </div>
                <div class="text-xs">
                    Added: {{ date('M jS, Y', strtotime($form -> created_at)) }} {{ date('g:i A', strtotime($form -> created_at)) }}
                </div>
            </div>

            <div class="flex justify-start items-center mt-3">

                <div>
                    <x-elements.button
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="active_form = '{{ $form_id }}'; window.open('/doc_management/admin/forms/form_fields/{{ $form_id }}', '_blank');">
                        <i class="fad fa-rectangle-wide mr-2"></i> Fillable Fields
                        <span class="inline-flex items-center px-2.5 py-0 ml-2 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $fields_count }}
                        </span>
                    </x-elements.button>
                </div>

                <div class="ml-3">
                    <x-elements.button
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="active_form = '{{ $form_id }}'">
                        <i class="fad fa-signature mr-2"></i> Signature Fields
                        <span class="inline-flex items-center px-2.5 py-0 ml-2 rounded-full text-xs bg-gray-100 text-gray-800">
                            8
                        </span>
                    </x-elements.button>
                </div>

                <div class="ml-3">
                    <x-elements.button
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    type="button"
                    @click="clear_form(); edit_form($event.target, `{{ $form_id }}`, `{{ $form_name_display}}`, `{{ $form_location }}`, `{{ $form_group_id }}`, `{{ $checklist_group_id }}`, `{{ $form_tag }}`, `{{ $state }}`, `{{ $helper_text }}`);
                    active_form = `{{ $form_id }}`;">
                        <i class="fad fa-edit mr-2"></i> Edit
                    </x-elements.button>
                </div>

                <div class="ml-3">
                    <x-elements.button
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="active_form = '{{ $form_id }}'; duplicate_form(`{{ $form_id }}`)">
                        <i class="fad fa-copy mr-2"></i> Duplicate
                    </x-elements.button>
                </div>

                <div class="ml-3">
                    <x-elements.button
                        :buttonClass="'success'"
                        :buttonSize="'md'"
                        type="button"
                        target="_blank"
                        @click="active_form = '{{ $form_id }}'; publish_form(`{{ $form_id }}`)">
                        <i class="fad fa-file-export mr-2"></i> Publish
                    </x-elements.button>
                </div>

                <div class="ml-3">
                    <x-elements.button
                        :buttonClass="'danger'"
                        :buttonSize="'md'"
                        type="button"
                        target="_blank"
                        @click="delete_form(`{{ $form_id }}`)">
                        <i class="fal fa-trash mr-2"></i> Delete
                    </x-elements.button>
                </div>

            </div>

        </li>

    @endforeach

</ul>


