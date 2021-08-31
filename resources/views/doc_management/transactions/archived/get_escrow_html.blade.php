<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $escrows -> onEachSide(1) -> links() }}
</div>

<div class="shadow border-b border-gray-200 sm:rounded-lg">

    <table class="w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('address', 'Address')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('agent', 'Agent')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Close Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Money In</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Money Out</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holding</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($escrows as $escrow)
                @php
                $address = $escrow -> address.' '.$escrow -> city.', '.$escrow -> state.' '.$escrow -> zip;
                if($escrow -> mls != '') {
                    $transaction = $escrow -> transaction_company;
                } else {
                    $transaction = $escrow -> transaction_skyslope;
                }
                $close_date = null;
                if($transaction) {
                    $close_date = substr($transaction -> escrowClosingDate, 0, 10);
                    if($transaction -> actualClosingDate != '') {
                        $close_date = substr($transaction -> actualClosingDate, 0, 10);
                    }
                }
                if(!$close_date) {
                    $close_date = $escrow -> contract_date;
                }

                // if($escrow -> address = '1450 Grandview Rd') {
                //     dd($escrow);
                // }

                $checks = $escrow -> checks;

                $escrow_total_in = $checks -> where('cleared', 'yes')
                -> where('amount', '>', '0')
                -> where('check_type', 'in')
                -> sum('amount');

                $escrow_total_out = $checks -> where('cleared', 'yes')
                -> where('amount', '>', '0')
                -> where('check_type', 'out')
                -> sum('amount');

                $escrow_total_left = $escrow_total_in - $escrow_total_out;

                $escrow_total_in = '$'.number_format($escrow_total_in, 0);
                $escrow_total_out = '$'.number_format($escrow_total_out, 0);
                $escrow_total_left = '$'.number_format($escrow_total_left, 0);
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        @if($transaction)
                        <a href="/transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="view-link px-4 py-3 bg-primary text-white text-center shadow rounded-md hover:bg-primary-dark hover:shadow-md" target="_blank">View</a>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $escrow -> agent }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $close_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $escrow_total_in }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $escrow_total_out }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-semibold">{{ $escrow_total_left }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $escrows -> onEachSide(1) -> links() }}
</div>
