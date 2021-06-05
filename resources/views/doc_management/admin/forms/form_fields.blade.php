<x-app-layout>
    @section('title') Form Fields @endsection

    <x-slot name="header">
        null
    </x-slot>

    <div class="page-container h-screen"
    x-data="fill_fields(), { active_page: 1, selected_field_type: '', active_field: '' }">

        <div class="w-full p-2 flex justify-around h-screen-8">

            <div class="flex items-center">

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="textbox"
                    :buttonClass="'default'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_type = 'textbox'; show_selected_field_type($event.currentTarget);">
                    <i class="fad fa-text fa-lg mr-2"></i> Textbox
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="date"
                    :buttonClass="'default'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_type = 'date'; show_selected_field_type($event.currentTarget);">
                    <i class="fad fa-calendar-alt fa-lg mr-2"></i> Date
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="price"
                    :buttonClass="'default'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_type = 'price'; show_selected_field_type($event.currentTarget);">
                    <span class="text-white mr-2">$0-9</span> Price/Number
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="checkbox"
                    :buttonClass="'default'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_type = 'checkbox'; show_selected_field_type($event.currentTarget);">
                    <i class="fad fa-square fa-lg mr-2"></i> Checkbox
                    </x-elements.button>

                </div>

                <div class="flex-auto px-2">

                    <x-elements.button
                    class="field-button"
                    type="button"
                    data-type="radio"
                    :buttonClass="'default'"
                    :buttonSize="'md'"
                    @click.prevent="selected_field_type = 'radio'; show_selected_field_type($event.currentTarget);">
                    <i class="fad fa-circle fa-lg mr-2"></i> Radio Button
                    </x-elements.button>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-9 w-full">

            <div class="col-span-8 relative">

                <div class="h-screen-92 overflow-y-auto pb-56 page-container">

                    <div class="w-3/4 mx-auto">

                        @foreach($pages as $page)

                            @php
                            $form_name = $form -> form_name_display;
                            $page_number = $page -> page_number;
                            $image_location = $page -> image_location;
                            @endphp

                            <div class="flex justify-between w-full bg-gray-300 p-2 text-sm text-gray-600 page-header-{{ $page_number }}">
                                <div>{{ $form_name }}</div>
                                <div>{{ $page_number }}</div>
                            </div>

                            <div class="form-page-container page-{{ $page_number }} relative"
                            @dblclick="add_field($event)">

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
                    @click.stop="active_page = {{ $page_number }}; go_to_page('{{ $page_number }}')">
                        <img src="/storage/{{ $image_location }}" class="w-100">
                        <div class="absolute bottom-1 right-1 px-1.5 py-0.5 text-center text-xs text-white bg-gray-600 rounded-lg">{{ $page_number }}</div>
                    </div>

                @endforeach

            </div>

        </div>




        {{-- Templates --}}

        <div id="field_template" class="hidden">

            <div class="field-div absolute"
            id="%%id%%"
            data-id="%%id%%"
            data-type="%%type%%"
            data-common-name-id=""
            x-bind:data-number-type="price_type"
            style="top: %%y_perc%%%; left: %%x_perc%%%; height: %%h_perc%%%; width: %%w_perc%%%;"
            x-data="{
                options_side: '',
                show_menu: false,
                field_type: '%%type%%',
                price_type: 'numeric'
            }"
            x-init="$parent.active_field = '%%id%%'"
            @click.stop="$parent.active_field = '%%id%%'"
            @click.away="$parent.active_field = ''"
            @dblclick.stop.prevent="return false;">

                <div class="resizers">
                    <div class="resizer top-left"></div>
                    <div class="resizer top-right"></div>
                    <div class="resizer bottom-left"></div>
                    <div class="resizer bottom-right"></div>
                </div>

                <div class="field-name draggable-handle flex items-center absolute top-0 w-full h-full whitespace-nowrap bg-blue-700 opacity-50 text-xs text-white px-1 overflow-hidden z-0"></div>

                <div class="" :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">

                    <div class="cursor-pointer"
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                    x-show="$parent.active_field === '%%id%%'"
                    @mouseenter="show_menu = true"
                    @mouseleave.debounce.150="show_menu = false">

                        <div class="absolute -top-3 py-1 px-2 bg-white border-2 z-10 rounded-sm shadow"
                        :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }">
                            <i class="fal fa-bars text-primary"></i>
                        </div>

                        <div class="absolute -top-3.5 p-3 w-max"
                        x-show.transition="show_menu"
                        :class="{ 'left-5': options_side === 'left', 'right-5': options_side === 'right' }">

                            <div class="flex justify-start items-center"
                            :class="{ 'flex-row': options_side === 'left', 'flex-row-reverse': options_side === 'right' }">

                                <x-elements.button
                                class="mx-2"
                                :buttonClass="'default'"
                                :buttonSize="'sm'"
                                type="button"
                                @click="$parent.copy_field(%%id%%);">
                                    <i class="fal fa-copy mr-2"></i> Copy
                                </x-elements.button>

                                <x-elements.button
                                class="mx-2"
                                :buttonClass="'default'"
                                :buttonSize="'sm'"
                                type="button"
                                {{-- @click="$parent.add_line()" --}}>
                                    <i class="fal fa-plus mr-2"></i> Add Line
                                </x-elements.button>

                                <x-elements.button
                                class="mx-3"
                                :buttonClass="'danger'"
                                :buttonSize="'sm'"
                                type="button"
                                @click="$parent.remove_field(%%id%%)">
                                    <i class="fal fa-ban mr-2"></i> Delete
                                </x-elements.button>

                            </div>

                        </div>

                    </div>

                    <div class="field-div-options absolute -bottom-1 w-max z-10"
                    :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                    x-show="$parent.active_field === '%%id%%'">

                        <div class="p-1 bg-white border-2 shadow absolute w-80 rounded"
                        :class="{ 'left-0 right-auto': options_side === 'left', 'right-0 left-auto': options_side === 'right' }"
                        x-show="field_type !== 'checkbox' && field_type !== 'radio'">

                            <div class="grid grid-cols-2"
                            x-show="field_type === 'price'">

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': price_type === 'numeric' }">
                                    <x-elements.radio
                                    name="price_type_%%id%%"
                                    class="price-type"
                                    checked="checked"
                                    value="numeric"
                                    :size="'sm'"
                                    :color="'blue'"
                                    :label="'Numeric'"
                                    x-model="price_type"/>
                                    <div class="text-xs text-gray-500">4,000.00</div>
                                </div>

                                <div class="p-2 rounded" :class="{ 'bg-blue-200': price_type === 'written' }">
                                    <x-elements.radio
                                    name="price_type_%%id%%"
                                    class="price-type"
                                    value="written"
                                    :size="'sm'"
                                    :color="'blue'"
                                    :label="'Written'"
                                    x-model="price_type"/>
                                    <div class="text-xs text-gray-500">Four Thousand</div>
                                </div>

                            </div>

                            <div class="my-3" x-show="field_type === 'price'"><hr></div>



                            <div class="p-1 mt-2">

                                <div class="flex justify-around items-center">

                                    @foreach($groups as $group)

                                        @php
                                        $data = $group['data'];
                                        $label = $group['label'];
                                        $type = $group['type'];
                                        $icon = $group['icon'];
                                        @endphp

                                        <div class="mx-1 relative"
                                        x-show="field_type === '{{ $type }}'">

                                            <div x-data="{ field_options: false }"
                                            @mouseover="field_options = true"
                                            @click="field_options = true"
                                            @mouseover.away="field_options = false">
                                                <x-elements.button
                                                class=""
                                                :buttonClass="'default'"
                                                :buttonSize="'md'"
                                                type="button">
                                                    <i class="fad {{ $icon }} mr-2"></i> {{ $label }}
                                                </x-elements.button>

                                                <div class="absolute top-6 left-0 pt-3"
                                                x-show.transition="field_options">

                                                    <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                        @if(count($data -> sub_groups) > 0)

                                                            @foreach($data -> sub_groups as $sub_group)

                                                                <li class="custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative">

                                                                    @if($label == 'Offices')

                                                                        <div class="flex justify-between items-center z-10">
                                                                            <i class="fal fa-angle-left ml-2 mr-3 z-0"></i>
                                                                            <span>{{ $sub_group -> sub_group_name }}</span>
                                                                        </div>

                                                                    @else

                                                                        <div class="flex justify-between items-center">
                                                                            <span>{{ $sub_group -> sub_group_name }}</span>
                                                                            <i class="fal fa-angle-right ml-3"></i>
                                                                        </div>

                                                                        @endif

                                                                    <ul class="custom-dropdown-content absolute hidden bg-blue-100 p-3 @if($label == 'Offices') -left-56 @else left-48 @endif top-0 z-20 border shadow rounded w-max">
                                                                        @foreach($sub_group -> common_fields as $field)
                                                                            <li class="common-name py-1 px-2 border-b w-56 cursor-pointer bg-white hover:bg-blue-200"
                                                                            data-id="{{ $field -> id }}"
                                                                            data-name="{{ $field -> field_name }}"
                                                                            @click.stop="field_options = false; select_common_name($event);">
                                                                                {!! str_replace($sub_group -> sub_group_name, '<span class="text-xs text-gray-600">'.$sub_group -> sub_group_name.' - </span>', $field -> field_name) !!}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>


                                                                </li>

                                                            @endforeach

                                                        @else

                                                            @foreach($data -> common_fields as $field)

                                                                <li class="common-name custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative"
                                                                data-id="{{ $field -> id }}"
                                                                data-name="{{ $field -> field_name }}"
                                                                @click.stop="field_options = false; select_common_name($event);">

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



                                    {{-- <div class="mx-1 relative"
                                    x-show="field_type === 'textbox'">

                                        <div x-data="{ field_options: false }"
                                        @mouseover="field_options = true"
                                        @click="field_options = true"
                                        @mouseover.away="field_options = false">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'default'"
                                            :buttonSize="'sm'"
                                            type="button">
                                                <i class="fad fa-users mr-2"></i> People
                                            </x-elements.button>

                                            <div class="absolute top-7 left-0 pt-3"
                                            x-show.transition="field_options">

                                                <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                    @foreach($common_fields_people -> sub_groups as $sub_group)

                                                        <li class="custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative">

                                                            <div class="flex justify-between items-center">
                                                                <span>{{ $sub_group -> sub_group_name }}</span>
                                                                <i class="fal fa-angle-right ml-3"></i>
                                                            </div>

                                                            <ul class="custom-dropdown-content absolute hidden bg-blue-100 p-3 left-48 top-0 border shadow rounded w-max">
                                                                @foreach($sub_group -> common_fields as $field)
                                                                    <li class="common-name py-1 px-2 border-b w-full cursor-pointer bg-white hover:bg-blue-200"
                                                                    data-id="{{ $field -> id }}"
                                                                    data-name="{{ $field -> field_name }}"
                                                                    @click.stop="field_options = false; select_common_name($event);">
                                                                        {!! str_replace($sub_group -> sub_group_name, '<span class="text-xs text-gray-600">'.$sub_group -> sub_group_name.' - </span>', $field -> field_name) !!}
                                                                    </li>
                                                                @endforeach
                                                            </ul>

                                                        </li>

                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="mx-1 relative"
                                    x-show="field_type === 'textbox'">

                                        <div x-data="{ field_options: false }"
                                        @mouseover="field_options = true"
                                        @click="field_options = true"
                                        @mouseover.away="field_options = false">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'default'"
                                            :buttonSize="'sm'"
                                            type="button">
                                                <i class="fad fa-building mr-2"></i> Property
                                            </x-elements.button>

                                            <div class="absolute top-7 left-0 pt-3"
                                            x-show.transition="field_options">

                                                <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                    @foreach($common_fields_property -> common_fields as $field)

                                                        <li class="common-name custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative"
                                                        data-id="{{ $field -> id }}"
                                                        data-name="{{ $field -> field_name }}"
                                                        @click.stop="field_options = false; select_common_name($event);">

                                                            <div class="flex justify-between items-center">
                                                                <span>{{ $field -> field_name }}</span>
                                                            </div>

                                                        </li>

                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="mx-1 relative"
                                    x-show="field_type === 'textbox'">

                                        <div x-data="{ field_options: false }"
                                        @mouseover="field_options = true"
                                        @click="field_options = true"
                                        @mouseover.away="field_options = false">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'default'"
                                            :buttonSize="'sm'"
                                            type="button">
                                                <i class="fad fa-users mr-2"></i> Office
                                            </x-elements.button>

                                            <div class="absolute top-7 left-0 pt-3"
                                            x-show.transition="field_options">

                                                <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                    @foreach($common_fields_offices -> sub_groups as $sub_group)

                                                        <li class="custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative">

                                                            <div class="flex justify-between items-center z-10">
                                                                <i class="fal fa-angle-left ml-2 mr-3 z-0"></i>
                                                                <span>{{ $sub_group -> sub_group_name }}</span>
                                                            </div>

                                                            <ul class="custom-dropdown-content absolute hidden bg-blue-100 p-3 -left-56 top-0 z-20 border shadow rounded w-max">
                                                                @foreach($sub_group -> common_fields as $field)
                                                                <li class="common-field py-1 px-2 border-b w-52 cursor-pointer bg-white hover:bg-blue-200"
                                                                data-id="{{ $field -> id }}"
                                                                data-name="{{ $field -> field_name }}"
                                                                @click.stop="field_options = false; select_common_name($event);">
                                                                    {!! str_replace($sub_group -> sub_group_name, '<span class="text-xs text-gray-600">'.$sub_group -> sub_group_name.' - </span>', $field -> field_name) !!}
                                                                </li>
                                                                @endforeach
                                                            </ul>

                                                        </li>

                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="mx-1 relative"
                                    x-show="field_type === 'date'">

                                        <div x-data="{ field_options: false }"
                                        @mouseover="field_options = true"
                                        @click="field_options = true"
                                        @mouseover.away="field_options = false">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'default'"
                                            :buttonSize="'sm'"
                                            type="button">
                                                <i class="fad fa-calendar mr-2"></i> Date
                                            </x-elements.button>

                                            <div class="absolute top-7 left-0 pt-3"
                                            x-show.transition="field_options">

                                                <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                    @foreach($common_fields_dates -> common_fields as $field)

                                                        <li class="common-field custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative"
                                                        data-id="{{ $field -> id }}"
                                                        data-name="{{ $field -> field_name }}"
                                                        @click.stop="field_options = false; select_common_name($event);">

                                                            <div class="flex justify-between items-center">
                                                                <span>{{ $field -> field_name }}</span>
                                                            </div>

                                                        </li>

                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="mx-1 relative"
                                    x-show="field_type === 'price'">

                                        <div x-data="{ field_options: false }"
                                        @mouseover="field_options = true"
                                        @click="field_options = true"
                                        @mouseover.away="field_options = false">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'default'"
                                            :buttonSize="'sm'"
                                            type="button">
                                                <i class="fad fa-calendar mr-2"></i> Price
                                            </x-elements.button>

                                            <div class="absolute top-7 left-0 pt-3"
                                            x-show.transition="field_options">

                                                <ul class="w-max text-sm bg-white p-2 relative border rounded shadow">

                                                    @foreach($common_fields_prices -> common_fields as $field)

                                                        <li class="common-field custom-dropdown p-2 border-b w-48 cursor-pointer hover:bg-blue-100 relative"
                                                        data-id="{{ $field -> id }}"
                                                        data-name="{{ $field -> field_name }}"
                                                        @click.stop="field_options = false; select_common_name($event);">

                                                            <div class="flex justify-between items-center">
                                                                <span>{{ $field -> field_name }}</span>
                                                            </div>

                                                        </li>

                                                    @endforeach

                                                </ul>

                                            </div>

                                        </div>

                                    </div> --}}

                                </div>

                            </div>

                            <div class="grid grid-cols-6 px-3 pt-3 pb-5">

                                <div class="col-span-5">
                                    <x-elements.input
                                    class="common-name-input"
                                    placeholder="Select Shared Name"
                                    data-label=""
                                    readonly
                                    :size="'md'"/>
                                </div>
                                <div class="col-span-1 ml-2">
                                    <x-elements.button
                                    class="clear-common-name-input"
                                    :buttonClass="'danger'"
                                    :buttonSize="'md'"
                                    type="button">
                                    <i class="fal fa-times"></i>
                                    </x-elements.button>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
