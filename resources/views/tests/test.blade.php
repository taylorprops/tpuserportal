{{-- blade-formatter-disable --}}
@php
$title = 'Add In House Employee';
$breadcrumbs = [
    ['In House Employees', '/employees/in_house'],
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

    <div class="pb-12 pt-2 h-screen-60">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="">

                <x-select-multiple  id="my_id" name="my_name[]" :size="'md'">
                    <option value="above">Above</option>
                    <option value="after">After</option>
                    <option value="back" selected>Back</option>
                    <option value="behind" selected>Behind</option>
                </x-select-multiple>

            </div>

        </div>

    </div>

</x-app-layout>
