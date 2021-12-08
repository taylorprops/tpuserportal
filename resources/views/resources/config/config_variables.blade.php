@php
$title = 'Config Variables';
$breadcrumbs = [
    ['Super Admin', ''],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="config()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <form id="config_add_form">

                <div x-data="{ show_add: false }">

                    <button
                    type="button"
                    class="button primary md mb-5"
                    @click="show_add = ! show_add">
                        Add Config Variable <i class="fal fa-angle-right ml-2"
                        x-bind:class="{ 'fa-rotate-90': show_add === true }"></i>
                    </button>

                    <div class="w-full sm:w-3/4 md:w-1/2 lg:w-1/3 p-5 mb-7 mt-3 bg-gray-50 shadow-inner rounded"
                    x-show="show_add" x-transition>

                        <div class="mb-3">
                            <input
                            type="text"
                            class="form-element input md"
                            name="config_key"
                            data-label="Key">
                        </div>

                        <div class="mb-3">
                            <textarea
                            class="form-element textarea md required"
                            name="config_value"
                            data-label="Value"></textarea>
                        </div>

                        <div class="mb-3">
                            <select
                            class="form-element select md"
                            name="value_type"
                            data-label="Value Type">
                                <option value="string">String</option>
                                <option value="array">Array</option>
                            </select>
                        </div>

                        <div class="flex justify-around p-5">
                            <button
                            type="button"
                            class="button primary md"
                            @click="config_add()">
                                <i class="fal fa-check mr-2"></i> Save
                            </button>
                        </div>

                    </div>

                </div>

            </form>

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8"
                x-data="table({
                    'container': $refs.container,
                    'data_url': '/resources/config/get_config_variables',
                    'length': '10',
                    'sort_by': 'config_key',
                    'button_export': false
                })">

                    <div class="table-container" x-ref="container"></div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
