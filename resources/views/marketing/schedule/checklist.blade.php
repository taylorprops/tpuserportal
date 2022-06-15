@php
$title = 'Schedule Checklist';
$breadcrumbs = [['Schedule', '/marketing/schedule'], [$title]];
@endphp
<x-app-layout>
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
            :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2"
        x-data="checklist()">

        <div class="max-w-1600-px mx-auto sm:px-6">

            <div class="mt-8">

                <div x-ref="checklist_div"></div>

            </div>

        </div>

        <x-modals.modal
            :modalWidth="'w-full sm:w-11/12 md:w-1/3 lg:w-1/4'"
            :modalTitle="'Addf Item'"
            :modalId="'show_add_item_modal'"
            x-show="show_add_item_modal">

            <div class="p-2 sm:p-4 lg:p-8">

                <form x-ref="add_item_form">

                    <div>
                        <x-select-multiple name="company_id[]" mulitple>
                            <option value="above">Above</option>
                            <option value="after">After</option>
                            <option value="back" selected>Back</option>
                            <option value="behind" selected>Behind</option>
                        </x-select-multiple>
                    </div>

                </form>

            </div>

        </x-modals.modal>

    </div>

</x-app-layout>
