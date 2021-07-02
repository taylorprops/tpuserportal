<x-app-layout>
    @section('title') Agents @endsection
    <x-slot name="header">
        Agents
    </x-slot>

    <div class="pb-12 pt-2 page-container z-0"
    x-data="agents()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">


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
