<x-app-layout>
    @section('title') Monitor @endsection
    <x-slot name="header">
        null
    </x-slot>

    <div class="bg-gray-50 p-5 pr-0 overflow-hidden">

        <div class="py-4 pl-6">
            <div class="flex justify-start items-center" x-data="controls()">
                <div class="mr-2">
                    <a href="javascript:void(0)" class="button lg no-text control-button"
                    x-on:click="active = '1'; play()"
                    x-bind:class="{'secondary': active === '1','primary': active !== '1' }">
                        <i class="fal fa-play fa-lg"></i>
                    </a>
                </div>
                <div class="mr-2">
                    <a href="javascript:void(0)" class="button primary lg no-text control-button"
                    x-on:click="active = '2'; stop()">
                        <i class="fal fa-stop fa-lg"></i>
                    </a>
                </div>
                <div class="mr-2">
                    <a href="javascript:void(0)" class="button primary lg no-text control-button"
                    x-on:click="active = '3'; refresh()">
                        <i class="fal fa-redo fa-lg"></i>
                    </a>
                </div>
                <div class="ml-4">
                    <select class="form-element select md" @change="resize_textareas($el.value)" data-label="Textarea Rows">
                        <option value="2">2</option>
                        <option value="4">4</option>
                        <option value="6">6</option>
                        <option value="8">8</option>
                        <option value="10">10</option>
                        <option value="12">12</option>
                        <option value="14">14</option>
                        <option value="16" selected>16</option>
                        <option value="18">18</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="monitor w-full h-screen-90 overflow-y-auto p-5"></div>

    </div>

</x-app-layout>
