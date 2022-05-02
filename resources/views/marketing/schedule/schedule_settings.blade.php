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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8" x-ref="settings_div"></div>

        </div>


        <x-modals.modal
            :modalWidth="'w-full sm:w-11/12 md:w-1/3 lg:w-1/4'"
            :modalTitle="'Delete Setting Item'"
            :modalId="'show_delete_modal'"
            x-show="show_delete_modal">

            <div class="p-2 sm:p-4 lg:p-8">

                <form x-ref="reassign_form">

                    <div x-ref="reassign_div"></div>

                    <div class="flex justify-around items-center pb-6 pt-12">
                        <button type="button" class="button primary xl" x-ref="save_delete_item" :disabled="reassign_disabled">Reassign and Delete Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                </form>

            </div>

        </x-modals.modal>


    </div>

</x-app-layout>
