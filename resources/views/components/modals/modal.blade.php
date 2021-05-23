

<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
    x-data="{ show_modal: false }"
    x-show="show_modal"
    x-on:show-modal-{{ $dispatchId }}.window="show_modal = true">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $width }} sm:p-6"  @click.away="show_modal = false">

            <div class="flex justify-between">

                <div class="text-2xl text-gray-600 mb-3">{{ $title }}</div>

                <a href="javascript:void(0)" @click="show_modal = false"><i class="fal fa-times fa-2x text-danger"></i></a>

            </div>

            {{ $slot }}

        </div>

    </div>

</div>
