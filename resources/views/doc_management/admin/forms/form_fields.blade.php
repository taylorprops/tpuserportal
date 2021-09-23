<x-app-layout>
    @section('title') Form Fields @endsection

    <x-slot name="header">
        null
    </x-slot>

    <div class="page-container h-screen"
    x-data="fields()">

        <div class="w-full p-2 flex justify-around h-screen-8">

            <div class="flex items-center">

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button primary md field-button"
                    data-type="textbox"
                    @click.prevent="selected_field_category = 'textbox'; show_selected_field_category($event.currentTarget);">
                        <i class="fad fa-text fa-lg mr-2"></i> Textbox
                    </button>

                </div>

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button primary md field-button"
                    data-type="date"
                    @click.prevent="selected_field_category = 'date'; show_selected_field_category($event.currentTarget);">
                        <i class="fad fa-calendar-alt fa-lg mr-2"></i> Date
                    </button>

                </div>

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button primary md field-button"
                    data-type="number"
                    @click.prevent="selected_field_category = 'number'; show_selected_field_category($event.currentTarget);">
                        <span class="text-white mr-2">$0-9</span> Price/Number
                    </button>

                </div>

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button primary md field-button"
                    data-type="checkbox"
                    @click.prevent="selected_field_category = 'checkbox'; show_selected_field_category($event.currentTarget);">
                        <i class="fad fa-square fa-lg mr-2"></i> Checkbox
                    </button>

                </div>

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button primary md field-button"
                    data-type="radio"
                    @click.prevent="selected_field_category = 'radio'; show_selected_field_category($event.currentTarget);">
                        <i class="fad fa-circle fa-lg mr-2"></i> Radio Button
                    </button>

                </div>

                <div class="flex-auto px-2">

                    <button
                    type="button"
                    class="button success lg"
                    data-type="radio"
                    @click="save_fields($event.currentTarget)">
                        Save Fields <i class="fal fa-check ml-2"></i>
                    </button>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-9 w-full"
        x-data="">

            <div class="col-span-8">

                <div class="h-screen-92 overflow-y-auto pb-56 forms-container"
                data-form-id="{{ $form_id }}">

                    <div class="w-3/4 mx-auto">

                        @foreach($pages as $page)

                            @php
                            $form_name = $form -> form_name_display;
                            $page_number = $page -> page_number;
                            $image_location = $page -> image_location;
                            @endphp

                            <div class="flex justify-between w-full bg-gray-300 p-2 text-sm page-header-{{ $page_number }}">
                                <div>{{ $form_name }}</div>
                                <div>{{ $page_number }}</div>
                            </div>

                            <div class="form-page-container page-{{ $page_number }} relative border"
                            data-page="{{ $page_number }}"
                            @dblclick.stop.prevent="add_field($event)">

                                <img src="/storage/{{ $image_location }}" class="w-100">

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

            <div class="col-span-1 bg-gray-300 h-screen-92 overflow-y-auto p-2">

                @foreach($pages as $page)

                    @php
                    $page_number = $page -> page_number;
                    $form_id = $page -> form_id;
                    $image_location = $page -> image_location;
                    @endphp

                    <div class="thumb-header-{{ $page_number }} w-3/4 mx-auto relative mb-2 cursor-pointer hover:shadow-md"
                    {{-- :class="{ 'opacity-30' : active_page !== {{ $page_number }}, 'opacity-100' : active_page === {{ $page_number }} }" --}}
                    >
                        <div @click.stop="active_page = {{ $page_number }}; go_to_page(`{{ $page_number }}`)">
                            <img src="/storage/{{ $image_location }}" class="w-100">
                        </div>


                        @if($loop -> last && $loop -> count > 1)
                            <div class="absolute bottom-1 right-1 px-1.5 py-0.5 text-center text-xs text-white bg-red-600 rounded-lg"
                            @click.stop="delete_page(`{{ $page_number }}`, `{{ $form_id }}`)">
                                <i class="fal fa-times"></i>
                            </div>
                        @else
                            <div class="absolute bottom-1 right-1 px-1.5 py-0.5 text-center text-xs text-white bg-gray-600 rounded-lg">
                                {{ $page_number }}
                            </div>
                        @endif

                    </div>


                @endforeach

            </div>

        </div>


        {{-- Templates --}}

        <template id="field_template" class="hidden">

            <div class="field-div absolute"
            id="field_%%id%%"
            data-id="%%id%%"
            data-category="%%category%%"
            data-common-field-id=""
            data-group-id="%%id%%"
            x-data="{
                field_category: '%%category%%'
            }"
            {{-- x-bind:data-number-type="number_type_%%id%%" --}}
            style="top: %%y_perc%%%; left: %%x_perc%%%; height: %%h_perc%%%; width: %%w_perc%%%;"
            x-bind:class="{ 'z-50': active_field === '%%id%%' }"
            x-init="
                draggable($el, '%%category%%'),
                resize($el)"
            @click.stop="
                active_field = '%%id%%';
                set_options_side($el);
                draggable($el, '%%category%%'),
                resize($el)"
            @click.outside="active_field = ''"
            @dblclick.stop.prevent>

                <div class="resizers">
                    <div class="resizer top-left"></div>
                    <div class="resizer top-right"></div>
                    <div class="resizer bottom-left"></div>
                    <div class="resizer bottom-right"></div>
                    <div class="group-label text-white h-1.5 w-1.5 rounded-full hidden z-20"
                    style="background: %%group_color%%"
                    x-bind:class="{ 'radio': field_category === 'radio' || field_category === 'checkbox' }"></div>
                </div>

                <div class="field-name field-name-%%id%% draggable-handle flex items-center absolute top-0 w-full h-full whitespace-nowrap opacity-80 text-xxs px-1 overflow-hidden"
                x-bind:class="{ 'bg-yellow-700 text-white': active_field === '%%id%%', 'bg-yellow-100 text-yellow-900': active_field !== '%%id%%' }"></div>

                <div class="" x-bind:class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                x-show="active_field === '%%id%%'">

                    <div class=""
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">

                        <div class="absolute -bottom-10 w-max"
                        :class="{ 'left-0': options_side === 'left', 'right-0': options_side === 'right' }">

                            <div class="flex justify-start items-center"
                            :class="{ 'flex-row': options_side === 'left', 'flex-row-reverse': options_side === 'right' }">

                                <button
                                type="button"
                                class="button primary sm"
                                x-show="field_category !== 'radio'"
                                x-bind:class="{ 'mr-2': options_side === 'left', 'ml-2': options_side === 'right' }"
                                @click="active_field = ''; copy_field('%%id%%', false);">
                                    <i class="fal fa-copy mr-2"></i> Copy
                                </button>

                                <button
                                type="button"
                                class="button primary sm"
                                x-show="field_category !== 'checkbox' && field_category !== 'date'"
                                @click="active_field = ''; copy_field('%%id%%', true);">
                                    <i class="fal fa-plus mr-2"></i> Add To Group
                                </button>

                                <button
                                type="button"
                                class="button danger sm mr-2"
                                @click="remove_field(%%id%%)">
                                    <i class="fal fa-ban mr-2"></i> Delete
                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="field-div-options absolute -bottom-14 w-max"
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">

                        <div class="p-4 bg-white border-2 shadow absolute w-96 rounded"
                        :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                        x-show="field_category !== 'checkbox' && field_category !== 'radio'">

                            <div class="grid grid-cols-2"
                            x-show="field_category === 'number'"
                            x-data="{
                                number_type: ''
                            }">

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': number_type === 'numeric' }">
                                    <div class="mb-2">
                                        <input
                                        type="radio"
                                        class="form-element radio md number-type"
                                        name="number_type_radio_%%id%%"
                                        id="numeric_%%id%%"
                                        value="numeric"
                                        data-label="Numeric"
                                        x-on:change="number_type = $el.checked ? 'numeric' : 'written';">
                                    </div>

                                    <div class="text-xs text-gray-500">4,000.00</div>

                                </div>

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': number_type === 'written' }">
                                    <div class="mb-2">
                                        <input
                                        type="radio"
                                        class="form-element radio md number-type"
                                        name="number_type_radio_%%id%%"
                                        id="written_%%id%%"
                                        value="written"
                                        data-label="Written"
                                        x-on:change="number_type = $el.checked ? 'written' : 'numeric';">
                                    </div>

                                    <div class="text-xs text-gray-500">Four Thousand</div>

                                </div>

                            </div>

                            <div class="my-3" x-show="field_category === 'number'"><hr></div>

                            <div class="font-sm my-2">Shared Field Name</div>

                            <div class="grid grid-cols-6 mb-3 place-content-center">

                                <div class="col-span-5">
                                    <input type="text" class="form-element input md common-field-input input-%%id%%" placeholder="Select Shared Name Below"
                                    data-label=""
                                    readonly>
                                    <input type="hidden">
                                </div>
                                <div class="col-span-1 ml-2">
                                    <button
                                    type="button"
                                    class="button danger md no-text clear-common-field-input"
                                    x-on:click="document.querySelector('.input-%%id%%').value = ''; document.querySelector('.field-name-%%id%%').innerText = '';">
                                        <i class="fal fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-1">

                                <div class="flex justify-around items-center">

                                    @foreach($groups as $group)

                                        @php
                                        $data = $group['data'];
                                        $label = $group['label'];
                                        $type = $group['type'];
                                        $icon = $group['icon'];
                                        @endphp

                                        <div class="mx-1 relative"
                                        x-show="field_category === '{{ $type }}'">

                                            <div x-data="{ field_options: false }"
                                            @mouseover="field_options = true"
                                            @click="field_options = true"
                                            @mouseover.outside="field_options = false">
                                                <button
                                                type="button"
                                                class="button primary sm">
                                                    <i class="fad {{ $icon }} mr-2"></i> {{ $label }} <i class="fal fa-angle-down ml-2"></i>
                                                </button>

                                                <div class="absolute top-5 left-0 pt-3"
                                                x-show="field_options" x-transition>

                                                    <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                        @if(count($data -> sub_groups) > 0)

                                                            @foreach($data -> sub_groups as $sub_group)

                                                                <li class="custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative">

                                                                    @if($label == 'Offices')

                                                                        <div class="flex justify-between items-center">
                                                                            <i class="fal fa-angle-left ml-2 mr-3"></i>
                                                                            <span>{{ $sub_group -> sub_group_name }}</span>
                                                                        </div>

                                                                    @else

                                                                        <div class="flex justify-between items-center">
                                                                            <span>{{ $sub_group -> sub_group_name }}</span>
                                                                            <i class="fal fa-angle-right ml-3"></i>
                                                                        </div>

                                                                        @endif

                                                                    <ul class="custom-dropdown-content absolute hidden bg-blue-100 p-3 @if($label == 'Offices') -left-56 @else left-48 @endif top-0 border shadow rounded w-max">
                                                                        @foreach($sub_group -> common_fields as $field)
                                                                            <li class="common-field py-1 px-2 border-b w-56 cursor-pointer bg-white hover:bg-blue-200"
                                                                            data-id="{{ $field -> id }}"
                                                                            data-name="{{ $field -> field_name }}"
                                                                            data-db-column-name="{{ $field -> db_column_name }}"
                                                                            data-field-type="{{ $field -> field_type }}"
                                                                            data-common-field-group-id="{{ $field -> group_id }}"
                                                                            data-common-field-sub-group-id="{{ $field -> sub_group_id }}"
                                                                            @click.stop="field_options = false; select_common_field($event);">
                                                                                {!! str_replace($sub_group -> sub_group_name, '<span class="text-xs">'.$sub_group -> sub_group_name.' - </span>', $field -> field_name) !!}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>


                                                                </li>

                                                            @endforeach

                                                        @else

                                                            @foreach($data -> common_fields as $field)

                                                                <li class="common-field custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative"
                                                                data-id="{{ $field -> id }}"
                                                                data-name="{{ $field -> field_name }}"
                                                                data-db-column-name="{{ $field -> db_column_name }}"
                                                                data-field-type="{{ $field -> field_type }}"
                                                                data-common-field-group-id="{{ $field -> group_id }}"
                                                                data-common-field-sub-group-id="{{ $field -> sub_group_id }}"
                                                                @click.stop="field_options = false; select_common_field($event);">

                                                                    <div class="flex justify-between items-center">
                                                                        <span>{{ $field -> field_name }}</span>
                                                                    </div>

                                                                </li>

                                                            @endforeach

                                                        @endif

                                                    </ul>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach


                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </template>

    </div>

</x-app-layout>
