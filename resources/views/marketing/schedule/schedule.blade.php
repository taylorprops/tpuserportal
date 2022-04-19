@php
$title = 'Marketing Schedule';
$breadcrumbs = [
    ['Marketing', ''],
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
    x-data="schedule()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="my-12">
                <button type="button" class="button primary lg"
                @click="show_add_item_modal = true">
                    Add Item <i class="fa-light fa-plus ml-3"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div>

                    <div class="border rounded-lg p-4 h-screen-60 overflow-auto">

                        <div x-ref="schedule_list_div"></div>


                    </div>

                </div>

                <div>

                </div>

            </div>

        </div>


        <x-modals.modal
            :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/2'"
            :modalTitle="'Add Marketing Item'"
            :modalId="'show_add_item_modal'"
            x-show="show_add_item_modal">

            <form x-ref="schedule_form">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <input type="date" class="form-element input md required" name="deploy_date" data-label="Deploy Date">
                    </div>

                    <div>
                        <select class="form-element select md required" name="category_id" data-label="Category">
                            <option value=""></option>
                            @foreach($categories as $category)
                                <option value="{{ $category -> id }}">{{ $category -> category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select class="form-element select md required" multiple name="states[]" data-label="States">
                            <option value=""></option>
                            @foreach($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </form>

        </x-modals.modal>

    </div>

</x-app-layout>
