<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $escrows -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('address', 'Address')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('agent', 'Agent')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Close Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holding</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($escrows as $escrow)
                @php
                $address = $escrow -> address.' '.$escrow -> city.', '.$escrow -> state.' '.$escrow -> zip;
                if($escrow -> TransactionId > 0) {
                    $transaction = $escrow -> transaction_skyslope;
                } else {
                    $transaction = $escrow -> transaction_company;
                }
                $close_date = $transaction ? substr($transaction -> actualClosingDate, 0, 10) : '';
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $escrow -> TransactionId }}
                        @if($transaction)
                        <a href="/transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="view-link px-4 py-3 bg-primary text-white text-center shadow rounded-md" target="_blank">View</a>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $escrow -> agent }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $close_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $escrows -> links() !!}
</div>
