{{-- blade-formatter-disable --}}
@php
$title = 'Tools';
$breadcrumbs = [
    [$title],
];
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
    x-data="tools()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="mt-24">

                <div class="text-xl font-semibold text-gray-700 mb-2">
                    TailwindCSS Safe List
                </div>
                <div class="text-gray-600 mb-4 border-b">Add classes to safelist</div>

                <div class="grid grid-cols-1 md:grid-cols-3">
                    <div>
                        <div class="text-lg mb-3">
                            Multiple Classes
                        </div>
                        <div class="w-60 mb-4">
                            <input type="text" class="form-element input md" x-ref="style" data-label="Style" placeholder="bg, hover:bg, border, etc.">
                        </div>
                        <div class="w-60 mb-4">
                            <input type="text" class="form-element input md" x-ref="level" data-label="Level" placeholder="50, 100, 200, etc.">
                        </div>

                        <div class="w-60 flex justify-around p-8">
                            <button type="button" class="button primary md" @click="create_classes($el, 'multiple')">Create Classes <i class="fa-duotone fa-cogs ml-2"></i></button>
                        </div>
                    </div>
                    <div>
                        <div class="text-lg mb-3">
                            Single Class
                        </div>
                        <div class="w-60 mb-4">
                            <input type="text" class="form-element input md" x-ref="single" data-label="Class Name" placeholder="bg-transparent, text-white, etc.">
                        </div>

                        <div class="w-60 flex justify-around p-8">
                            <button type="button" class="button primary md" @click="create_classes($el, 'single')">Create Class <i class="fa-duotone fa-cogs ml-2"></i></button>
                        </div>
                    </div>
                    <div class="">
                        <div x-ref="classes_div"></div>
                    </div>
                </div>




            </div>

        </div>

    </div>

</x-app-layout>
