<x-app-layout>
    @section('title') Tests - alpine @endsection
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test
        </h2>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- ----------------------------------------------- --}}


                    <div class="w-full p-2 flex justify-around"
                    x-data="field_buttons()">



                        <x-elements.button
                            :buttonClass="'default'"
                            :buttonSize="'md'"
                            @click="active($event.currentTarget)"
                            type="button">
                            <i class="fal fa-check mr-2"></i> Textbox
                        </x-elements.button>

                        <x-elements.button
                            :buttonClass="'default'"
                            :buttonSize="'md'"
                            @click="active($event.currentTarget)"
                            type="button">
                            <i class="fal fa-check mr-2"></i> Other
                        </x-elements.button>

                        <x-elements.button
                            :buttonClass="'default'"
                            :buttonSize="'md'"
                            @click="active($event.currentTarget)"
                            type="button">
                            <i class="fal fa-check mr-2"></i> ON more
                        </x-elements.button>

                    </div>


                    {{-- ----------------------------------------------- --}}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
