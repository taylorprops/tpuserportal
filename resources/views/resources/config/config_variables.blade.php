<x-app-layout>
    @section('title') Config Variables @endsection
    <x-slot name="header">
        Config Variables
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="config()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <form id="config_add_form">

                <div x-data="{ show_add: false }">

                    <x-elements.button
                        class="mb-5"
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="show_add = ! show_add">
                        Add Config Variable <i class="fal fa-angle-right ml-2"
                        x-bind:class="{ 'fa-rotate-90': show_add === true }"></i>
                    </x-elements.button>

                    <div class="w-full sm:w-3/4 md:w-1/2 lg:w-1/3 p-5 mb-7 mt-3 bg-gray-50 shadow-inner rounded"
                    x-show="show_add" x-transition>

                        <div class="mb-3">
                            <x-elements.input
                            name="config_key"
                            data-label="Key"
                            :size="'md'"/>
                        </div>

                        <div class="mb-3">
                            <x-elements.textarea
                            name="config_value"
                            data-label="Value"
                            :size="'md'"/>
                        </div>

                        <div class="mb-3">
                            <x-elements.select
                            name="value_type"
                            data-label="Value Type"
                            :size="'md'">
                                <option value="string">String</option>
                                <option value="array">Array</option>
                            </x-elements.select>
                        </div>

                        <div class="flex justify-around p-5">
                            <x-elements.button
                                :buttonClass="'primary'"
                                :buttonSize="'md'"
                                type="button"
                                @click="config_add()">
                                <i class="fal fa-check mr-2"></i> Save
                            </x-elements.button>
                        </div>

                    </div>

                </div>

            </form>

            <div class="no-wrap">

                <table id="config_table" class="data-table hover order-column row-border">

                    <thead>
                        <tr>
                            <th>Key</th>
                            <th class="w-screen-50">Value</th>
                            <th>Type</th>
                        </tr>
                    </thead>

                    <tbody>

                    </body>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
