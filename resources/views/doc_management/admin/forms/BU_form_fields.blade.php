<x-app-layout>
    @section('title') Form Fields @endsection

    <x-slot name="header">
        null
    </x-slot>

    <div class="page-container h-screen"
    x-data="fill_fields(), { active_page: 1, selected_field_category: '', active_field: '' }">

        <div class="w-full p-2 flex justify-around h-screen-8">

            <div class="flex items-center">

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="textbox"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_category = 'textbox'; show_selected_field_category($event.currentTarget);">
                    <i class="fad fa-text fa-lg mr-2"></i> Textbox
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="date"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_category = 'date'; show_selected_field_category($event.currentTarget);">
                    <i class="fad fa-calendar-alt fa-lg mr-2"></i> Date
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="number"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_category = 'number'; show_selected_field_category($event.currentTarget);">
                    <span class="text-white mr-2">$0-9</span> Price/Number
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="checkbox"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_category = 'checkbox'; show_selected_field_category($event.currentTarget);">
                    <i class="fad fa-square fa-lg mr-2"></i> Checkbox
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="radio"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_category = 'radio'; show_selected_field_category($event.currentTarget);">
                    <i class="fad fa-circle fa-lg mr-2"></i> Radio Button
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    type="button"
                    data-type="radio"
                    :buttonClass="'success'"
                    :buttonSize="'lg'"
                    @click="save_fields($event.currentTarget)">
                    Save Fields <i class="fal fa-check ml-2"></i>
                    </x-elements.button>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-9 w-full">

            <div class="col-span-8 relative">

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

                            <div class="form-page-container page-{{ $page_number }} relative"
                            data-page="{{ $page_number }}"
                            @dblclick.stop.prevent="active_field = ''; add_field($event)">

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
                    $image_location = $page -> image_location;
                    @endphp

                    <div class="thumb-header-{{ $page_number }} w-3/4 mx-auto relative mb-2 cursor-pointer hover:shadow-md"
                    {{-- :class="{ 'opacity-30' : active_page !== {{ $page_number }}, 'opacity-100' : active_page === {{ $page_number }} }" --}}
                    @click.stop="active_page = {{ $page_number }}; go_to_page(`{{ $page_number }}`)">
                        <img src="/storage/{{ $image_location }}" class="w-100">
                        <div class="absolute bottom-1 right-1 px-1.5 py-0.5 text-center text-xs text-white bg-gray-600 rounded-lg">{{ $page_number }}</div>
                    </div>

                @endforeach

            </div>

        </div>




        {{-- Templates --}}

        <template id="field_template" class="hidden">

            <div class="field-div absolute animate__animated animate__fadeIn"
            id="field_%%id%%"
            data-id="%%id%%"
            data-category="%%category%%"
            data-common-field-id=""
            data-group-id="%%id%%"
            x-bind:data-number-type="number_type"
            style="top: %%y_perc%%%; left: %%x_perc%%%; height: %%h_perc%%%; width: %%w_perc%%%;"
            x-data="{
                options_side: '',
                field_category: '%%category%%',
                number_type: 'numeric',
                is_group: false,
                add_type: ' Line'
            }"
            x-init="$parent.active_field = '%%id%%'; add_type = field_category == 'radio' ? ' Radio' : ' Line';"
            :class="{ 'z-50': $parent.active_field === '%%id%%' }"
            @click.stop="$parent.active_field = '%%id%%'"
            @click.outside="$parent.active_field = ''"
            @dblclick.stop.prevent="return false;">

                @php $input_id = time() * rand(); @endphp

                <div class="resizers">
                    <div class="resizer top-left"></div>
                    <div class="resizer top-right"></div>
                    <div class="resizer bottom-left"></div>
                    <div class="resizer bottom-right"></div>
                    <div class="group-label font-bold text-indigo-500"
                    :class="{ 'radio': field_category === 'radio' || field_category === 'checkbox' }"
                    x-show="is_group">G</div>
                </div>

                <div class="field-name field-name-{{ $input_id }} draggable-handle flex items-center absolute top-0 w-full h-full whitespace-nowrap opacity-50 text-xs text-white px-1 overflow-hidden"
                :class="{ 'bg-yellow-600': $parent.active_field === '%%id%%', 'bg-blue-600': $parent.active_field !== '%%id%%' }"></div>

                <div class="" :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                x-show="$parent.active_field === '%%id%%'">

                    <div class=""
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">

                        <div class="absolute -top-3.5 py-3 w-max"
                        :class="{ 'left-0': options_side === 'left', 'right-0': options_side === 'right' }">

                            <div class="flex justify-start items-center"
                            :class="{ 'flex-row': options_side === 'left', 'flex-row-reverse': options_side === 'right' }">

                                <x-elements.button
                                class="mx-1"
                                :buttonClass="'primary'"
                                :buttonSize="'sm'"
                                type="button"
                                @click="$parent.active_field = ''; $parent.copy_field('%%id%%', false);">
                                    <i class="fal fa-copy mr-2"></i> Copy
                                </x-elements.button>

                                <x-elements.button
                                class="mx-1"
                                :buttonClass="'primary'"
                                :buttonSize="'sm'"
                                type="button"
                                x-show="field_category !== 'checkbox' && field_category !== 'date'"
                                @click="$parent.active_field = ''; $parent.copy_field('%%id%%', true)">
                                    <i class="fal fa-plus mr-2"></i> Add  <span class="ml-1" x-text="add_type"></span>
                                </x-elements.button>

                                <x-elements.button
                                class="mx-1"
                                :buttonClass="'danger'"
                                :buttonSize="'sm'"
                                type="button"
                                @click="$parent.remove_field(%%id%%)">
                                    <i class="fal fa-ban mr-2"></i> Delete
                                </x-elements.button>

                            </div>

                        </div>

                    </div>

                    <div class="field-div-options absolute -bottom-1 w-max"
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">

                        <div class="p-2 bg-white border-2 shadow absolute w-96 rounded"
                        :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                        x-show="field_category !== 'checkbox' && field_category !== 'radio'">

                            <div class="grid grid-cols-2"
                            x-show="field_category === 'number'">

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': number_type === 'numeric' }">
                                    <x-elements.radio
                                    name="number_type_%%id%%"
                                    class="number-type"
                                    checked="checked"
                                    value="numeric"
                                    :size="'sm'"
                                    :color="'blue'"
                                    :label="'Numeric'"
                                    x-model="number_type"/>
                                        <div class="text-xs text-gray-500">4,000.00</div>
                                </div>

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': number_type === 'written' }">
                                    <x-elements.radio
                                    name="number_type_%%id%%"
                                    class="number-type"
                                    value="written"
                                    :size="'sm'"
                                    :color="'blue'"
                                    :label="'Written'"
                                    x-model="number_type"/>
                                        <div class="text-xs text-gray-500">Four Thousand</div>
                                </div>

                            </div>

                            <div class="my-3" x-show="field_category === 'number'"><hr></div>

                            <div class="font-sm my-2">Shared Field Name</div>

                            <div class="grid grid-cols-6 mb-3">

                                <div class="col-span-5">
                                    <x-elements.input
                                    class="common-field-input input-{{ $input_id }}"
                                    placeholder="Select Shared Name Below"
                                    data-label=""
                                    readonly
                                    :size="'md'"/>
                                </div>
                                <div class="col-span-1 ml-2">
                                    <x-elements.button
                                    class="clear-common-field-input"
                                    :buttonClass="'danger'"
                                    :buttonSize="'md'"
                                    type="button"
                                    x-on:click="document.querySelector('.input-{{ $input_id }}').value = ''; document.querySelector('.field-name-{{ $input_id }}').innerText = '';">
                                    <i class="fal fa-times"></i>
                                    </x-elements.button>
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
                                                <x-elements.button
                                                class=""
                                                :buttonClass="'primary'"
                                                :buttonSize="'sm'"
                                                type="button">
                                                    <i class="fad {{ $icon }} mr-2"></i> {{ $label }} <i class="fal fa-angle-down ml-2"></i>
                                                </x-elements.button>

                                                <div class="absolute top-6 left-0 pt-3"
                                                x-transition="field_options">

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
