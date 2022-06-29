<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $escrows -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr>
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('address', 'Address')</th>
                <th scope="col">@sortablelink('agent', 'Agent')</th>
                <th scope="col">Close Date</th>
                <th scope="col">Money In</th>
                <th scope="col">Money Out</th>
                <th scope="col">Holding</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($escrows as $escrow)
                {{-- blade-formatter-disable --}}
                @php
                    $address = $escrow -> address.' '.$escrow -> city.', '.$escrow -> state.' '.$escrow -> zip;
                    if ($escrow -> mls != '') {
                        $transaction = $escrow -> transaction_company;
                    } else {
                        $transaction = $escrow -> transaction_skyslope;
                    }
                    $close_date = null;
                    if ($transaction) {
                        $close_date = substr($transaction -> escrowClosingDate, 0, 10);
                        if ($transaction -> actualClosingDate != '') {
                            $close_date = substr($transaction -> actualClosingDate, 0, 10);
                        }
                    }
                    if (!$close_date) {
                        $close_date = $escrow -> contract_date;
                    }

                    // if($escrow -> address = '1450 Grandview Rd') {
                    //     dd($escrow);
                    // }

                    $checks = $escrow -> checks;

                    $escrow_total_in = $checks
                        -> where('cleared', 'yes')
                        -> where('amount', '>', '0')
                        -> where('check_type', 'in')
                        -> sum('amount');

                    $escrow_total_out = $checks
                        -> where('cleared', 'yes')
                        -> where('amount', '>', '0')
                        -> where('check_type', 'out')
                        -> sum('amount');

                    $escrow_total_left = $escrow_total_in - $escrow_total_out;

                    $escrow_total_in = '$'.number_format($escrow_total_in, 0);
                    $escrow_total_out = '$'.number_format($escrow_total_out, 0);
                    $escrow_total_left = '$'.number_format($escrow_total_left, 0);
                @endphp
{{-- blade-formatter-enable --}}
                <tr>
                    <td>
                        @if ($transaction)
                            <a href="/transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="view-link button primary md"
                                target="_blank">View</a>
                        @endif
                    </td>
                    <td>{{ $address }}</td>
                    <td>{{ $escrow -> agent }}</td>
                    <td>{{ $close_date }}</td>
                    <td>{{ $escrow_total_in }}</td>
                    <td>{{ $escrow_total_out }}</td>
                    <td class="p-2 text-gray-700 font-semibold">{{ $escrow_total_left }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $escrows -> onEachSide(1) -> links() }}
</div>
