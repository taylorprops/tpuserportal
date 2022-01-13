<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $transactions -> links() !!}
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr>
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('status', 'Status')</th>
                <th scope="col">@sortablelink('address', 'Address')</th>
                <th scope="col">@sortablelink('agent_name', 'Agent')</th>
                <th scope="col">@sortablelink('listingDate', 'List Date')</th>
                <th scope="col">@sortablelink('actualClosingDate', 'Close Date')</th>
                <th scope="col">@sortablelink('data_source', 'Data Source')</th>
                <th scope="col">Doc Count</th>
            </tr>
        </thead>
        <tbody>
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
                    <td>
                        <a href="transactions_archived_view/{{ $transaction -> listingGuid }}/{{ $transaction -> saleGuid }}" class="view-link button primary md" target="_blank">View</a>
                    </td>
                    <td>{{ ucwords($transaction -> status) }}</td>
                    <td>{{ $address }}</td>
                    <td>{{ $transaction -> agent_name }}</td>
                    <td>{{ substr($transaction -> listingDate, 0, 10) }}</td>
                    <td>{{ $close_date }}</td>
                    <td>{{ $data_source }}</td>
                    <td>{{ $docs_count }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $transactions -> links() !!}
</div>
