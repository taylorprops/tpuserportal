<x-app-layout>
    @section('title') Config Variables @endsection
    <x-slot name="header">
        Config Variables
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="config()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    <form id="config_add_form">

                        <div class="font-lg text-secondary"><i class="fal fa-plus mr-2"></i> Add Config Variable</div>
                        <div class="mb-3 flex justify-start items-end pb-5 mb-5 border-b">

                            <div class="ml-3">
                                <x-elements.input
                                name="config_key"
                                data-label="Key"
                                :size="'md'"/>
                            </div>

                            <div class="ml-3">
                                <x-elements.textarea
                                class="h-8"
                                name="config_value"
                                data-label="Value"
                                :size="'md'"/>
                            </div>

                            <div class="ml-3">
                                <x-elements.select
                                name="value_type"
                                data-label="Value Type"
                                :size="'md'">
                                    <option value="string">String</option>
                                    <option value="array">Array</option>
                                </x-elements.select>
                            </div>

                            <div class="ml-2">
                                <x-elements.button
                                    :buttonClass="'primary'"
                                    :buttonSize="'md'"
                                    type="button"
                                    @click="config_add()">
                                    <i class="fal fa-check mr-2"></i> Save
                                </x-elements.button>
                            </div>

                        </div>

                    </form>

                    <div class="no-wrap">

                        <table id="config_table" class="data-table hover order-column row-border text-gray-600 animate__animated animate__fadeIn">

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

        </div>

    </div>

</x-app-layout>
