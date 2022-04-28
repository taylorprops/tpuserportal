@php
$title = 'Schedule Settings';
$breadcrumbs = [
    ['Marketing', ''],
    ['Schedule', '/marketing/schedule'],
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
    x-data="schedule_settings()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12 pt-16">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                @foreach($categories as $category)

                    <div class="border rounded-md p-0"
                    x-data="{ show_add_item: false }">
                        <div class="bg-gray-50 text-lg p-4 rounded-t-md border-b-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    {{ ucwords($category) }}
                                </div>
                                <div>
                                    <button type="button" class="button primary md" @click="show_add_item = true;">Add <i class="fa-light fa-plus ml-2"></i></button>
                                </div>
                            </div>
                            <div class="flex justify-start p-4 mt-3" x-show="show_add_item" x-transition x-trap="show_add_item">
                                <div>
                                    <input type="text" class="form-element input md" x-ref="add_{{ $category }}_input">
                                </div>
                                <div class="ml-2">
                                    <button type="button" class="button primary md" @click="settings_save_add_item($el, '{{ $category }}', $refs.add_{{ $category }}_input)">Save <i class="fa-light fa-check ml-2"></i></button>
                                </div>
                            </div>
                        </div>
                        <div data-type="{{ $category }}"> </div>
                    </div>

                @endforeach

            </div>


        </div>


        <x-modals.modal
            :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/2'"
            :modalTitle="'Add Marketing Item'"
            :modalId="'show_delete_modal'"
            x-show="show_delete_modal">

            <form x-ref="schedule_form">

                <div class="p-2 sm:p-4 lg:p-8">

                    <div x-ref="reassign_div"></div>

                    <div class="flex justify-around items-center pb-6 pt-12">
                        <button type="button" class="button primary xl" x-ref="save_delete_item">Reassign and Delete Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                </div>

            </form>

        </x-modals.modal>


    </div>

</x-app-layout>
