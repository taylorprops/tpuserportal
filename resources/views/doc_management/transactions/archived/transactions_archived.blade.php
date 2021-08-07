<x-app-layout>
    @section('title') Archives @endsection
    <x-slot name="header">
        Archives
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="archives()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">

                        <div class="d-flex justify-content-center mb-2">
                            {!! $transactions -> links() !!}
                        </div>

                        <div class="table-div shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">
                                    <tr>
                                        <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">List Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Close Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                    @php
                                    $property = json_decode($transaction -> property);
                                    $address = $property -> streetNumber.' '.$property -> streetAddress.' '.$property -> city.', '.$property -> state.' '.$property -> zip;
                                    $agent = '';
                                    if($transaction -> agent_details) {
                                        $agent = $transaction -> agent_details -> nickname.' '.$transaction -> agent_details -> last;
                                    }
                                    @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="px-4 py-3 bg-primary text-white text-center shadow rounded-md" target="_blank">View</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucwords($transaction -> status) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $address }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ substr($transaction -> listingDate, 0, 10) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ substr($transaction -> actualClosingDate, 0, 10) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tbody>

                                </body>

                            </table>

                        </div>

                        <div class="d-flex justify-content-center mt-2">
                            {!! $transactions -> links() !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
