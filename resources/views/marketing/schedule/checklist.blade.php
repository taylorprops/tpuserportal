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
            :modalWidth="'w-full sm:w-11/12 md:w-1/3'"
            :modalTitle="'Add/Edit Item'"
            :modalId="'show_add_item_modal'"
            x-show="show_add_item_modal">

            <div class="p-2 sm:p-4 lg:p-8">

                <form x-ref="add_item_form">

                    <div class="grid grid-cols-2 gap-4">

                        <div class="space-y-2">

                            <div>
                                <select class="form-element select md" name="company_id[]" x-ref="company_id" data-label="Company">
                                    @foreach ($settings -> where('category', 'company') as $setting)
                                        <option value="{{ $setting -> id }}">{{ $setting -> item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select class="form-element select md" name="recipient_id[]" x-ref="recipient_id" data-label="Recipient">
                                    @foreach ($settings -> where('category', 'recipient') as $setting)
                                        <option value="{{ $setting -> id }}">{{ $setting -> item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <select class="form-element select md" name="states" x-ref="states" multiple data-label="States">

                            </select>
                        </div>

                    </div>

                    <div>
                        <div x-ref="item" name="item"></div>
                    </div>

                    <div class="flex justify-around p-4 pt-8">
                        <button type="button" class="button primary md" @click="save_item($el)">Save Item <i class="fa-light fa-check ml-2"></i></button>
                    </div>

                    <input type="hidden" name="id" x-ref="id">

                </form>

            </div>

        </x-modals.modal>

    </div>

</x-app-layout>
