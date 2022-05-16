@php
$title = 'Agent Database';
$breadcrumbs = [
    ['Heritage Financial', ''],
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
    x-data="agent_database()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            @if(auth() -> user() -> level != 'loan_officer')

                <div>

                    <button type="button" class="button primary sm mt-4"
                    @click="show_add = ! show_add" x-transition>Add New List <i class="fal fa-plus ml-2"></i></button>

                    <div class="w-700-px border rounded-md p-3 mt-3"
                    x-show="show_add">
                        <div class="text-lg font-semibold text-gray-500">Add New List</div>
                        <form id="add_list_form" enctype="multipart/form-data">
                            <div class="flex justify-start">
                                <div class="flex-grow">
                                    <input type="file" class="form-element input md" name="upload_input" id="upload_input" x-on:change="show_file_names($el, false);" x-ref="upload_input">
                                </div>
                                <div class="ml-2">
                                    <button class="button primary md"
                                    @click.prevent="add_list($el)">
                                        Upload List <i class="fa fa-upload ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="text-sm text-gray-400 mt-2">
                            Upload excel or csv<br>
                            No header row<br>
                            Columns: First Name, Last Name, Email, Cell Phone, Street, City, State, Zip, Company, Start Date
                        </div>
                    </div>

                </div>

            @endif

            <div class="">

                <div class="flex flex-col w-full">

                    <div class=""
                    x-data="table({
                        'container': $refs.container,
                        'data_url': '/heritage_financial/agent_database/get_agent_database',
                        'length': '25',
                        'sort_by': 'last_name',
                        'button_export': true,
                        'dates': {
                            'col': 'start_date',
                            'text': 'Start Date'
                        },

                    })">

                        <div class="table-container" x-ref="container"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
