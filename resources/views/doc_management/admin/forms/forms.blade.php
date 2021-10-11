@php
$title = 'Forms';
$breadcrumbs = [
    [$title],
];
$active_tab = $form_groups -> first() -> id; $default_state = $form_groups -> first() -> state;
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="page-container pt-2"
        x-data="forms('{{ $active_tab }}')">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="h-screen-90">

                <div class="flex justify-between mb-2">

                    <div class="search-container relative">

                        <input type="text" class="form-element input md"
                            id="search"
                            placeholder="Search"
                            data-label=""
                            @keyup="search_forms($event.target);"/>

                        <div class="absolute top-10 left-0 bg-white rounded border border-gray-300 shadow-md p-2 w-screen sm:w-screen-70 md:w-screen-50 z-10"
                        x-show="show_search_results" x-transition
                        x-on:click.outside="show_search_results = false; document.querySelector('#search').value = '';">
                            <ul id="search_results"></ul>
                        </div>

                    </div>

                    <button
                    type="button"
                    class="button primary md"
                    x-on:click="show_modal = true; clear_form()">
                        <i class="fal fa-plus mr-2"></i> Add Form
                    </button>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="col-span-1">

                        <ul class="w-full border border-gray-100 h-screen-70 overflow-auto">

                            @foreach($form_groups as $form_group)

                                @php
                                $form_group_id = $form_group -> id;
                                $state = $form_group -> state;
                                $count = $form_group -> forms -> count();
                                @endphp

                                <li class="form-group-{{ $form_group_id }} border border-b p-3 cursor-pointer hover:bg-primary-light hover:text-white @if($loop -> first) bg-primary-dark text-white @endif"
                                    data-form-group-id="{{ $form_group_id }}"
                                    :class="{ 'bg-primary-dark text-white': active_tab === '{{ $form_group_id }}' }"
                                    @click.prevent="active_tab = '{{ $form_group_id }}';
                                    document.querySelector('#form_group_id').value = '{{ $form_group_id }}';
                                    document.querySelector('#state').value = '{{ $state }}';
                                    get_forms()">

                                    <div class="flex justify-between items-center">

                                        <span>{{ $form_group -> group_name }}</span>

                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $count }}
                                        </span>

                                    </div>
                                </li>

                            @endforeach

                        </ul>

                    </div>

                    <div class="col-span-3">

                        <div id="forms_div"></div>

                    </div>

                </div>

            </div>

        </div>


        <x-modals.modal
            :modalWidth="'w-9/12'"
            :modalTitle="'Add Form'"
            :modalId="'show_modal'"
            x-show="show_modal">

            <form id="upload_form" enctype="multipart/form-data">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="border rounded p-3"">

                        <div class="my-3">

                            <div id="current_form" class="mb-2"></div>

                            <input type="file" class="form-element input md required"
                            name="upload"
                            id="upload"
                            accept="application/pdf"
                            @change.stop="get_upload_text(event)"/>

                        </div>

                        <div class="mb-3 mt-5 pb-3 bg-gray-100 rounded hidden form-names-div">

                            <div class="flex justify-start">
                                <h5 class="text-secondary ml-2 my-1" @click="show_form_names = !show_form_names">Select and/or Edit Form Name</h5>
                                <a href="javascript:void(0)" @click="show_form_names = !show_form_names">
                                    <i class="fal fa-angle-right text-secondary fa-lg ml-3 mt-2" :class="{ '' : !show_form_names, 'fa-rotate-90' : show_form_names }"></i>
                                </a>
                            </div>
                            <div class="form-names p-t-0 p-2" x-show="show_form_names">

                            </div>

                        </div>

                        <div class="mt-4">
                            <input type="text" class="form-element input md required"
                            id="form_name_display"
                            name="form_name_display"
                            data-label="Form Name"/>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">

                            <div class="mt-4">

                                <div>
                                    <select
                                    class="form-element select md required"
                                    id="checklist_group_id"
                                    name="checklist_group_id"
                                    data-label="Checklist Group"
                                    placeholder="Checklist Group">
                                        <option value=""></option>
                                        @foreach($checklist_groups as $checklist_group)
                                        <option value="{{ $checklist_group -> id }}">{{ $checklist_group -> group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <select
                                    class="form-element select md required"
                                    id="form_group_id"
                                    name="form_group_id"
                                    data-label="Form Group"
                                    placeholder="Form Group">
                                        <option value=""></option>
                                        @foreach($form_groups as $form_group)
                                        <option value="{{ $form_group -> id }}"  @if($form_group -> id  == $active_tab) selected @endif>{{ $form_group -> group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="mt-4">

                                <div>
                                    <select
                                    class="form-element select md"
                                    id="form_tag"
                                    name="form_tag"
                                    data-label="Form Tag"
                                    placeholder="Form Tag">
                                        <option value=""></option>
                                        @foreach($form_tags as $form_tag)
                                            <option value="{{ $form_tag -> id }}">{{ $form_tag -> tag_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <select
                                    class="form-element select md required"
                                    id="state"
                                    name="state"
                                    data-label="State"
                                    placeholder="State">
                                        <option value=""></option>
                                        <option value="All">All</option>
                                        @foreach($active_states as $active_state)
                                        <option value="{{ $active_state }}" @if($active_state == $default_state) selected @endif>{{ $active_state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                        </div>


                        <div class="my-5">

                            <textarea
                            class="form-element textarea md required"
                            id="helper_text"
                            name="helper_text"
                            data-label="Helper Text"></textarea>

                        </div>

                    </div>

                    <div class="border rounded p-3">

                        <div id="form_preview" class="h-full"></div>

                    </div>

                </div>

                <div class="border-top mt-5 sm:mt-4 ">

                    <button
                    type="button"
                    class="button primary lg mr-5"
                    @click="save_form($event.target); show_loading_button($event.target, 'Saving Form'); $event.target.disabled = true;">
                        <i class="fal fa-check mr-2"></i> Save Form
                    </button>

                    <button
                    type="button"
                    class="button danger md ml-5"
                    @click="show_modal = false">
                        <i class="fal fa-times mr-2"></i> Cancel
                    </button>


                </div>

                <input type="hidden" name="form_id" id="form_id">

            </form>

        </x-modals.modal>

    </div>

    {{-- Template --}}

    <div id="form_name_template" class="hidden">
        <div class="flex justify-start title-option w-100 h-9 form-name-div">
            <div class="w-1/7 mr-2 flex-none my-3">
                <button
                type="button"
                class="button primary sm add-title"
                @click="add_form_name($event.target.closest('.form-name-div'))">
                    <i class="fal fa-check mr-2"></i> Select
                </button>
            </div>
            <div class="flex-grow pr-2 my-3">
                <input type="text" class="form-name-title form-element input sm"
                value="%%title%%">
            </div>
        </div>
    </div>

    <div id="search_results_li_template" class="hidden">
        <li class="border-b p-2 cursor-pointer hover:bg-gray-100"
        x-on:click="show_result('%%form_group_id%%', '%%form_id%%')">
            <div class="grid grid-cols-6">
                <div class="col-span-4 mr-3">%%form_name_display%%</div>
                <div class="col-span-2">%%state%% - %%form_group%%</div>
            </div>
        </li>
    </div>

</x-app-layout>
