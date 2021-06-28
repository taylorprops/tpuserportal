<x-app-layout>
    @section('title') Checklists @endsection
    <x-slot name="header">
        <i class="fad fa-tasks mr-3"></i> Checklists
    </x-slot>

    <div class="page-container"
    x-data="checklists()"
    x-init="get_checklist_locations();
    location_id = '{{ $checklist_locations -> first() -> id }}';
    $fetch('/transactions/get_locations').then(data => locations = data);
    $fetch('/transactions/get_form_groups').then(data => form_groups = data);
    $fetch('/transactions/get_property_types').then(data => property_types = data);
    $fetch('/transactions/get_property_sub_types').then(data => property_sub_types = data);">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="h-screen-90">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="col-span-1">

                        <ul class="w-full border border-gray-100 h-screen-85 overflow-auto rounded">

                            @foreach($checklist_locations as $checklist_location)

                                @php
                                $checklist_location_id = $checklist_location -> id;
                                @endphp

                                <li class="form-group form-group-{{ $checklist_location_id }} border border-b p-3 cursor-pointer hover:bg-primary-light hover:text-white @if($loop -> first) rounded bg-primary-darker text-white @endif"
                                    data-form-group-id="{{ $checklist_location_id }}"
                                    :class="{ 'active bg-primary-darker text-white': location_id === '{{ $checklist_location_id }}' }"
                                    @click.prevent="location_id = '{{ $checklist_location_id }}';
                                    active_type = 'listing';
                                    get_checklists()">

                                    {{ $checklist_location -> state }} | {{ $checklist_location -> location }}

                                </li>

                            @endforeach

                        </ul>

                    </div>

                    <div class="col-span-3">

                        <div class="h-screen-85">

                            <div id="checklist_locations"></div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Modals --}}

        <x-modals.modal
        :modalWidth="'w-4/5'"
        :modalTitle="'<span class=\'modal-title\'>Add Checklist Items</span>'"
        :modalId="'show_add_items_modal'"
        x-show="show_add_items_modal">

            <div class="grid grid-cols-3">

                <div class="col-span-1 mr-6">

                    <div class="mb-4">
                        <x-elements.input
                        placeholder="Search..."
                        :size="'md'"
                        @keyup="search_forms($event.currentTarget.value)"/>
                    </div>

                    <div class="mb-4">
                        <x-elements.select
                        data-label="Association"
                        :size="'md'"
                        {{-- x-init="active_form_group = form_groups[0].id" --}}
                        @change="active_form_group = $event.currentTarget.value">
                            <option value=""></option>
                            <template x-for="form_group in form_groups" :key="form_group.id">
                                <option :value="form_group.id" x-text="form_group.group_name"></option>
                            </template>
                        </x-elements.select>
                    </div>

                    <div class="h-screen-65 overflow-auto">

                        @foreach($form_groups as $form_group)

                            @php
                            $form_group_id = $form_group -> id;
                            $form_group_name = $form_group -> group_name;
                            $forms = $form_group -> forms;
                            @endphp

                            <div class="mr-4" x-show="active_form_group === '{{ $form_group -> id }}' || active_form_group === ''">

                                <div class="p-2 font-semibold bg-gray-100 rounded-t"
                                x-show="searching_form_groups === false">
                                    {{ $form_group_name }}
                                </div>

                                <ul class="mb-6">
                                    @foreach($forms as $form)

                                        @php
                                        $form_id = $form -> id;
                                        $form_name = $form -> form_name_display;
                                        $checklist_group_id = $form -> checklist_group_id;
                                        @endphp

                                        <li class="form-name p-2 border-b text-sm text-gray-700 hover:bg-gray-50"
                                        data-form-name="{{ $form_name }}">

                                            <div class="flex justify-between items-center">

                                                <div>{{ $form_name }}</div>

                                                <x-elements.button
                                                    :buttonClass="'primary'"
                                                    :buttonSize="'sm'"
                                                    type="button"
                                                    @click="add_checklist_item(`{{ $checklist_group_id }}`, `{{ $form_id }}`, `{{ $form_name }}`)">
                                                        <i class="fal fa-plus mr-2"></i> Add
                                                </x-elements.button>

                                            </div>

                                            <div class="text-xs text-gray-400"
                                            x-show="searching_form_groups === true">
                                                {{ $form_group_name }}
                                            </div>

                                        </li>

                                    @endforeach
                                </ul>

                            </div>

                        @endforeach

                    </div>

                </div>

                <div class="col-span-2">

                    <div class="h-screen-80 overflow-auto">

                        @foreach($checklist_groups as $checklist_group)

                            @php
                            $items = $checklist_group -> checklist_items;
                            @endphp

                            <div class="rounded-lg border mb-4">

                                <div class="text-lg bg-gray-100 rounded-t-lg pl-3 py-2">
                                    {{ $checklist_group -> group_name }}
                                </div>

                                <div class="checklist-group p-3" data-checklist-group-id="{{ $checklist_group -> id }}"></div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </x-modals.modal>

        <x-modals.modal
        :modalWidth="'w-300'"
        :modalTitle="'Delete Checklist'"
        :modalId="'show_confirm_modal'"
        x-show="show_confirm_modal">

            <div class="text-center w-full my-3">
                Are you sure you want to delete this checklist?
            </div>
            <div class="flex justify-around pt-5 border-t">

                <x-elements.button
                    class=""
                    :buttonClass="'danger'"
                    :buttonSize="'md'"
                    type="button"
                    @click="show_confirm_modal = false">
                    <i class="fal fa-times mr-2"></i> Cancel
                </x-elements.button>

                <x-elements.button
                    id="confirm"
                    :buttonClass="'primary'"
                    :buttonSize="'md'"
                    type="button">
                    <i class="fal fa-check mr-2"></i> Continue
                </x-elements.button>

            </div>

        </x-modals.modal>


        <x-modals.modal
        :modalWidth="'w-1/3'"
        :modalTitle="'<span x-text=\'checklist_modal_title\'></span>'"
        :modalId="'show_checklist_modal'"
        x-show="show_checklist_modal">

            <form id="checklist_form">

                <div class="mb-5">
                    <x-elements.select
                    id="location_id"
                    name="location_id"
                    class="required"
                    data-label="Checklist Location"
                    :size="'md'">
                        <option value=""></option>
                        <template x-for="location in locations" :key="location.id">
                            <option :data-state="location.state" :value="location.id" x-text="location.state+' | '+location.location"></option>
                        </template>
                    </x-elements.select>
                </div>

                <div class="mb-5">
                    <x-elements.select
                    id="sale_rent"
                    name="sale_rent"
                    class="required"
                    data-label="Sale/Rental"
                    :size="'md'"
                    @change="for_sale = $event.target.value;
                    let prop_type = document.querySelector('#property_type_id');
                    show_property_sub_type = $event.target.value === 'sale' && prop_type.options[prop_type.selectedIndex].text === 'Residential' ? true : false;">
                        <option value=""></option>
                        <option value="sale">Sale</option>
                        <option value="rental">Rental</option>
                    </x-elements.select>
                </div>

                <div class="mb-5">
                    <x-elements.select
                    id="property_type_id"
                    name="property_type_id"
                    class="required"
                    data-label="Property Type"
                    :size="'md'"
                    @change="show_property_sub_type = $event.target.options[$event.target.selectedIndex].text === 'Residential' && document.querySelector('#sale_rent').value === 'sale' ? true : false">
                        <option value=""></option>
                        <template x-for="prop_type in property_types" :key="prop_type.id">
                            <option :value="prop_type.id" x-text="prop_type.property_type"></option>
                        </template>

                    </x-elements.select>
                </div>

                <div class="mb-5"
                x-show.transition="show_property_sub_type">
                    <x-elements.select
                    id="property_sub_type_id"
                    name="property_sub_type_id"
                    x-bind:class="{ 'required': show_property_sub_type === true }"
                    data-label="Sale Type"
                    :size="'md'">
                        <option value=""></option>
                        <template x-for="property_sub_type in property_sub_types" :key="property_sub_type.id">
                            <option :value="property_sub_type.id" x-text="property_sub_type.property_sub_type"></option>
                        </template>

                    </x-elements.select>
                </div>

                <div class="mb-5">
                    <x-elements.select
                    id="checklist_type"
                    name="checklist_type"
                    class="required"
                    data-label="Checklist Type"
                    :size="'md'">
                        <option value=""></option>
                        <option value="listing">Listing</option>
                        <option value="contract">Contract/Lease</option>
                    </x-elements.select>
                </div>

                <div class="mb-5">
                    <x-elements.select
                    id="represent"
                    name="represent"
                    class="required"
                    data-label="Represent"
                    :size="'md'">
                        <option value=""></option>
                        <option value="seller">Seller/Owner</option>
                        <option value="buyer">Buyer/Renter</option>
                    </x-elements.select>
                </div>

                <div class="w-full flex justify-around py-5">
                    <x-elements.button
                        id="save_checklist"
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="save_checklist()">
                        <i class="fal fa-check mr-2"></i> Save Checklist
                    </x-elements.button>

                </div>

                <input type="hidden" name="id" id="id">

            </form>

        </x-modals.modal>


    </div>

    <template id="form_template">

        <div class="form px-2 border-b flex justify-between items-center text-sm form-%%name%%"
        data-form-id="%%form_id%%"
        data-checklist-id="%%checklist_id%%"
        data-checklist-group-id="%%checklist_group_id%%">

            <div class="py-2 flex justify-start items-center cursor-pointer">
                <div class="pr-5 item-handle"><i class="fal fa-bars"></i></div>
                <div>%%form_name%%</div>
            </div>

            <div class="py-2 flex justify-end items-center">

                <div class="mr-8">

                    <x-elements.toggle
                    id="toggle_%%name%%"
                    :size="'md'"
                    :label="'Required'"
                    @onchange=""/>

                </div>

                <div>
                    <x-elements.button
                        :buttonClass="'danger'"
                        :buttonSize="'md'"
                        type="button"
                        @click.stop="$event.target.closest('.form-%%name%%').remove()">
                            <i class="fal fa-times"></i>
                    </x-elements.button>
                </div>

            </div>

        </div>

    </template>

</x-app-layout>
