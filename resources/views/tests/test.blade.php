<x-app-layout>
    @section('title') Test @endsection
    <x-slot name="header">
        null
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="relative">
            <div class=" w-48 h-48 overflow-y-auto bg-blue-700">

                <div class="absolute left-0 w-48 h-96 bg-yellow-400 overflow-hidden">waht</div>

                <div class="absolute top-10 left-10 w-96 bg-red-700" style="height: 20px;">
                    <div class="overflow-visible w-96">
                    asdfasdf asdfasdfs
                </div>
            </div>

        </div>




        {{-- <div class="overflow-y-auto h-screen-92 w-1/2 relative">

            <div class="w-full  pb-56 relative  page-container">

                @foreach($pages as $page)

                    @php
                    $form_name = $form -> form_name_display;
                    $page_number = $page -> page_number;
                    $image_location = $page -> image_location;
                    @endphp

                    <div class="form-page-container page-{{ $page_number }} relative">

                        <img src="/storage/{{ $image_location }}" class="w-100">

                        <div class="field-div absolute" style="top: 0%; left: 90%; height: 10%; width: 20%;">

                            <div class="field-div-options">

                                <div class="p-2 bg-white border-2 shadow absolute w-96 rounded right-0 left-auto">

                                    <div class="my-2 text-gray-600">
                                        Shared Field Name
                                    </div>

                                    <div class="grid grid-cols-6">
                                        <div class="col-span-5">
                                            <x-elements.input
                                            id="common_name"
                                            name="common_name"
                                            placeholder="Select Shared Name"
                                            data-label=""
                                            readonly
                                            :size="'md'"/>
                                        </div>
                                        <div class="col-span-1 ml-2">
                                            <x-elements.button
                                            class=""
                                            :buttonClass="'danger'"
                                            :buttonSize="'md'"
                                            type="button">
                                            <i class="fal fa-times"></i>
                                            </x-elements.button>
                                        </div>
                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div> --}}

    </div>

</x-app-layout>
