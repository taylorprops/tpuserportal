<x-app-layout>
    @section('title') Monitor @endsection
    <x-slot name="header">
        null
    </x-slot>

    <div class="bg-gray-50 p-5 pr-0 overflow-hidden">

        <div class="py-4 pl-6">
            <div class="flex justify-start items-center" x-data="controls()">
                <div class="mr-2">
                    <a href="javascript:void(0)" class="p-2 border rounded shadow control-button"
                    x-on:click="active = '1'; play()"
                    x-bind:class="{'bg-primary text-white': active === '1','bg-gray-100 text-gray-600': active !== '1' }">
                        <i class="fal fa-play fa-lg"></i>
                    </a>
                </div>
                <div class="mr-2">
                    <a href="javascript:void(0)" class="p-2 border rounded shadow control-button"
                    x-on:click="active = '2'; stop()">
                        <i class="fal fa-stop fa-lg"></i>
                    </a>
                </div>
                <div class="mr-2">
                    <a href="javascript:void(0)" class="p-2 border rounded shadow control-button"
                    x-on:click="active = '3'; refresh()">
                        <i class="fal fa-redo fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="monitor w-full h-screen-90 overflow-y-auto p-5"></div>

    </div>

</x-app-layout>
