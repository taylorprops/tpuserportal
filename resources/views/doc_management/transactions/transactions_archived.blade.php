<x-app-layout>
    @section('title') Archives @endsection
    <x-slot name="header">
        Archives
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="archives()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="">

                <div class="no-wrap">

                    <table id="archives_table" class="data-table hover nowrap order-column row-border" width="100%">

                        <thead>
                            <tr>
                                <th width="100"></th>
                                <th>Name</th>
                            </tr>
                        </thead>

                        <tbody>

                        </body>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
