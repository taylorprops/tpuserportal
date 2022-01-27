@php
$title = 'Agents';
$breadcrumbs = [
    ['Employees', ''],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2 page-container z-0"
    x-data="agents()">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">


            <div class="no-wrap">

                <table id="agents_table" class="data-table hover nowrap order-column row-border" width="100%">

                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>

                    <tbody>

                    </body>

                </table>

            </div>


            {{-- xxxxxxxxxxxxxxxxxxxx --}}

        </div>


    </div>

</x-app-layout>
