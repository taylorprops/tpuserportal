@php
    $title = 'Upload List';
    $breadcrumbs = [
    ['Marketing', ''],
    [$title],
    ];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="">

                <select class="form-element select md">

                </select>

            </div>



            <div class="text-lg font-semibold text-gray-500">Add New List</div>
            <form id="add_list_form" enctype="multipart/form-data">
                <div class="flex justify-start">
                    <div class="flex-grow">
                        <input type="file" class="form-element input md" name="agent_list" id="agent_list" x-on:change="show_file_names($el, false);" x-ref="agent_list">
                    </div>
                    <div class="ml-2">
                        <button class="button primary md" <blade click|.prevent%3D%26%2334%3Badd_list(%24el)%26%2334%3B%3E>
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

</x-app-layout>