<x-app-layout>
    @section('title') Agents @endsection
    <x-slot name="header">
        Agents
    </x-slot>

    <div class="pb-12 pt-2 page-container">

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- xxxxxxxxxxxxxxxxxxxx --}}


                    <div class="no-wrap">

                        <table id="agents_table" class="data-table hover nowrap order-column row-border text-gray-600 animate__animated animate__fadeIn hidden" width="100%">

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

        </div>

    </div>

</x-app-layout>
