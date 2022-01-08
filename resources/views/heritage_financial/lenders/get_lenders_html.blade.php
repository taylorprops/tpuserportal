<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr id="sortable_tr">
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('company_name', 'Lender')</th>
                {{-- <th scope="col">@sortablelink('loan_officer_last', 'Loan Officer')</th>
                <th scope="col">@sortablelink('street', 'Address')</th>
                <th scope="col">@sortablelink('settlement_date', 'Close Date')</th>
                <th scope="col">@sortablelink('borrower_last', 'Borrower')</th>
                <th scope="col">@sortablelink('loan_amount', 'Loan Amount')</th>
                <th scope="col">@sortablelink('processor_id', 'Processor')</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($lenders as $lender)
                <tr>
                    <td>
                    <a href="/heritage_financial/lenders/view_lender/{{ $lender -> uuid }}" class="view-link button primary md" target="_blank">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td>{{ $lender -> lender_status }}</td>
                    <td>{{ $lender_officer }}</td>
                    <td>{{ $address }}</td>
                    <td>{{ $lender -> settlement_date }}</td>
                    <td>
                        {{ $lender -> borrower_last.', '.$lender -> borrower_first }}
                        @if($lender -> co_borrower_last != '')
                            <br>
                            {{ $lender -> co_borrower_last.', '.$lender -> co_borrower_first }}
                        @endif
                    </td>
                    <td>${{ number_format($lender -> lender_amount) }}</td>
                    <td>@if($lender -> processor) {{ $lender -> processor -> fullname }} @endif</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>
