<x-app-layout>

    @section('title') Forms @endsection

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Forms
        </h2>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">
                    @php $dispatchId = time(); @endphp
                    <x-elements.button
                        @click="$dispatch('show-modal-{{ $dispatchId }}')"
                        :colorClass="'default'"
                        :size="'md'"
                        type="button">
                        <i class="fal fa-plus mr-2"></i> Add Form
                    </x-elements.button>

                    <x-modals.modal
                        :dispatchId="$dispatchId"
                        :width="'w-9/12'"
                        :title="'Add Form'">

                        <form id="upload_form" enctype="multipart/form-data">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div class="border rounded p-3">

                                    <div class="p-2">

                                        <x-elements.input-file :size="'md'" name="upload" id="upload" accept="application/pdf" @change="get_upload_text(event)"/>

                                    </div>

                                    <div class="p-2 form-names p-3 bg-gray-100 rounded"></div>

                                    <div class="p-2">
                                        <x-elements.input :size="'md'" id="form_name" name="form_name" type="text" data-label="Form Name"/>
                                    </div>


                                </div>

                                <div class="border rounded p-3">

                                    <div id="form_preview" class="h-screen-70"></div>

                                </div>

                            </div>

                            <div class="border-top mt-5 sm:mt-4 ">

                                <x-elements.button
                                    class="mr-5"
                                    @click="save_add_form(), show_modal = false"
                                    :colorClass="'default'"
                                    :size="'lg'"
                                    type="button">
                                    <i class="fal fa-check mr-2"></i> Save Form
                                </x-elements.button>

                                <x-elements.button
                                    class="ml-5"
                                    @click="show_modal = false"
                                    :colorClass="'danger'"
                                    :size="'md'"
                                    type="button">
                                    <i class="fal fa-times mr-2"></i> Cancel
                                </x-elements.button>


                            </div>

                        </form>

                    </x-modals.modal>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
