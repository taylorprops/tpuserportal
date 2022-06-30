{{-- blade-formatter-disable --}}
@php
$title = 'Marketing Checklist';
$breadcrumbs = [['Schedule', '/marketing/schedule'], [$title]];
@endphp
{{-- blade-formatter-enable --}}
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
            :modalWidth="'w-full sm:w-11/12 md:w-1/2'"
            :modalTitle="'Add/Edit Item'"
            :modalId="'show_add_item_modal'"
            x-show="show_add_item_modal"
            :clickOutside="'show_add_item_modal = true;'">

            <div class="p-2 sm:p-4 lg:p-8 lg:pt-2">

                <form x-ref="add_item_form">

                    <div class="flex justify-end " x-show="show_delete">
                        <button type="button" class="button danger sm" @click="delete_item()">Delete <i class="fa-light fa-times ml-2"></i></button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div class="space-y-2">

                            <div>
                                <select class="form-element select md" name="company_id" x-ref="company_id" data-label="Company">

                                    @foreach ($settings -> where('category', 'company') as $setting)

                                        <option value="{{ $setting -> id }}">{{ $setting -> item }}</option>

                                    @endforeach

                                </select>
                            </div>
                            <div>
                                <select class="form-element select md" name="states[]" x-ref="states_select" multiple data-label="States">

                                </select>
                            </div>
                        </div>

                        <div>
                            <select class="form-element select md h-44" name="recipient_ids[]" x-ref="recipient_ids" multiple data-label="Recipients">

                                @foreach ($settings -> where('category', 'recipient') as $setting)

                                    <option value="{{ $setting -> id }}">{{ $setting -> item }}</option>

                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="mt-4">
                        <div x-ref="data" id="data"></div>
                    </div>

                    <div class="flex justify-around p-4 pt-8">
                        <button type="button" class="button primary md" @click="save_item($el)">Save Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                    <input type="hidden" x-ref="state">
                    <input type="hidden" x-ref="recipient_id">
                    <input type="hidden" name="id" x-ref="id">

                </form>

            </div>

        </x-modals.modal>

    </div>

</x-app-layout>
