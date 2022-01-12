<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $loans -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr id="sortable_tr">
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('loan_status', 'Status')</th>
                <th scope="col">@sortablelink('loan_officer_last', 'Loan Officer')</th>
                <th scope="col">@sortablelink('street', 'Address')</th>
                <th scope="col">@sortablelink('settlement_date', 'Close Date')</th>
                <th scope="col">@sortablelink('borrower_last', 'Borrower')</th>
                <th scope="col">@sortablelink('loan_amount', 'Loan Amount')</th>
                <th scope="col">@sortablelink('processor_id', 'Processor')</th>
                <th scope="col">@sortablelink('created_at', 'Added On')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                @php
                $address = $loan -> street.' '.$loan -> city.', '.$loan -> state.' '.$loan -> zip;
                $loan_officer = $loan -> loan_officer_last.', '.$loan -> loan_officer_first;
                @endphp
                <tr>
                    <td>
                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="view-link button primary md">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td>{{ $loan -> loan_status }}</td>
                    <td>{{ $loan_officer }}</td>
                    <td>{{ $address }}</td>
                    <td>{{ $loan -> settlement_date }}</td>
                    <td>
                        {{ $loan -> borrower_last.', '.$loan -> borrower_first }}
                        @if($loan -> co_borrower_last != '')
                            <br>
                            {{ $loan -> co_borrower_last.', '.$loan -> co_borrower_first }}
                        @endif
                    </td>
                    <td>${{ number_format($loan -> loan_amount) }}</td>
                    <td>@if($loan -> processor) {{ $loan -> processor -> fullname }} @endif</td>
                <td>{{ date('M jS, Y', strtotime($loan -> created_at)) }} {{ date('g:i A', strtotime($loan -> created_at)) }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $loans -> onEachSide(1) -> links() }}
</div>
