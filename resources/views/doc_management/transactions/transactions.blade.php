{{-- blade-formatter-disable --}}
@php
$title = 'Transactions';
$breadcrumbs = [
    [$title],
];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">



        </div>

    </div>

</x-app-layout>
