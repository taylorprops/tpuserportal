<x-app-layout>
    @section('title') Monitor @endsection
    <x-slot name="header">
        null
    </x-slot>

    <div class="w-screen h-screen-95 bg-gray-50 pr-4">

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
                    x-on:click="active = '2'; stop()"
                    x-bind:class="{'bg-primary text-white': active === '2','bg-gray-100 text-gray-600': active !== '2' }">
                        <i class="fal fa-stop fa-lg"></i>
                    </a>
                </div>
                <div class="mr-2">
                    <a href="javascript:void(0)" class="p-2 border rounded shadow control-button"
                    x-on:click="active = '3'; refresh()"
                    x-bind:class="{'bg-primary text-white': active === '3','bg-gray-100 text-gray-600': active !== '3' }">
                        <i class="fal fa-redo fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        <iframe class="monitor" width="100%" height="100%"></iframe>

    </div>

</x-app-layout>
