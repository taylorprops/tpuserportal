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

                <div class="p-2 sm:p-4 lg:p-8">

                    <div class="grid grid-cols-1 md:grid-cols-9 gap-8">

                        <div class="col-span-2">
                            <input type="date" class="form-element input md required" name="deploy_date" data-label="Deploy Date">
                        </div>

                        <div class="col-span-3">
                            <select class="form-element select md required" name="category_id" data-label="Category">
                                <option value=""></option>
                                @foreach($categories as $category)
                                    <option value="{{ $category -> id }}">{{ $category -> category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-4">

                            <div class="text-gray-500 text-sm">States</div>

                            <div class="flex justify-between shadow rounded-md">

                                @foreach($states as $state)

                                    <label for="{{ $state }}"
                                    class="@if($loop -> first) rounded-l-md border-r border-gray-200 @elseif ($loop -> last) rounded-r-md @else border-r border-gray-200 @endif
                                    flex justify-around items-center py-2 w-full  cursor-pointer"
                                    x-data="{ active: false }"
                                    x-ref="{{ $state }}"
                                    :class="active === true ? 'bg-primary text-white' : 'color-gray-700 hover:bg-gray-50'">
                                        {{ $state }}
                                        <input type="checkbox" class="hidden" name="state[]" id="{{ $state }}" value="{{ $state }}"
                                        @change="active = $el.checked">
                                    </label>

                                @endforeach

                            </div>

                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mt-8">

                        <div class="">
                            <select class="form-element select md required" name="company" data-label="Company">
                                <option value=""></option>
                                @foreach($companies as $company)
                                    <option value="{{ $company -> id }}">{{ $company -> company }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-around items-center pb-6 pt-12">
                        <button type="button" class="button primary xl" @click="save_add_item($el)">Save Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                </div>

            </form>

        </x-modals.modal>

    </div>

</x-app-layout>
