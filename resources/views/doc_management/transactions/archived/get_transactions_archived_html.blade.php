<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $transactions -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('status', 'Status')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('address', 'Address')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('agent_name', 'Agent')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('listingDate', 'List Date')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('actualClosingDate', 'Close Date')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('data_source', 'Data Source')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc Count</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($transactions as $transaction)
                @php
                $address = $transaction -> address.' '.$transaction -> city.', '.$transaction -> state.' '.$transaction -> zip;
                if($transaction -> data_source == 'mls_company') {
                    $data_source = 'Company Site';
                } else if($transaction -> data_source == 'skyslope') {
                    $data_source = 'Skyslope';
                }
                $docs_count = count($transaction -> docs_listing) + count($transaction -> docs_sale);
                $close_date = substr($transaction -> escrowClosingDate, 0, 10);
                if($transaction -> actualClosingDate != '') {
                    $close_date = substr($transaction -> actualClosingDate, 0, 10);
                }
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="view-link button primary md" target="_blank">View</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucwords($transaction -> status) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction -> agent_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ substr($transaction -> listingDate, 0, 10) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $close_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data_source }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $docs_count }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $transactions -> links() !!}
</div>
