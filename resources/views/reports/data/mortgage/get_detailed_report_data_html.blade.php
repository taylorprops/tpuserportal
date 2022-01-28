<div class="flex justify-between flex-wrap mb-2 pagination-div">
    <div class="w-full">
        {!! $loans -> links() !!}
    </div>
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr id="sortable_tr">
                <th></th>
                <th scope="col">@sortablelink('loan_officer_1_id', 'Loan Officer')</th>
                <th scope="col">@sortablelink('borrower_last', 'Borrower(s)')</th>
                <th scope="col">@sortablelink('street', 'Address')</th>
                <th scope="col">@sortablelink('settlement_date', 'Settle Date')</th>
                <th scope="col">@sortablelink('loan_amount', 'Loan Amount')</th>
                <th scope="col">@sortablelink('company_commission', 'Commission')</th>
                <th scope="col">@sortablelink('loan_type', 'Program')</th>
                <th scope="col">@sortablelink('loan_purpose', 'Purpose')</th>
                <th scope="col">@sortablelink('mortgage_type', 'Mort Type')</th>
                <th scope="col">@sortablelink('lender_uuid', 'Lender')</th>
                <th scope="col">@sortablelink('state', 'State')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                @php
                $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                if($loan -> co_borrower_first != '') {
                    $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                }
                $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
                @endphp
                <tr>
                    <td><a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" target="_blank" class="default">View</a></td>
                    <td>@if($loan -> loan_officer_1) {{ $loan -> loan_officer_1 -> last_name.', '.$loan -> loan_officer_1 -> first_name }} @endif</td>
                    <td class="whitespace-nowrap">{!! $borrower !!}</td>
                    <td class="whitespace-nowrap">{!! $address !!}</td>
                    <td>{{ $loan -> settlement_date }}</td>
                    <td>${{ number_format($loan -> loan_amount) }}</td>
                    <td>${{ number_format($loan -> company_commission) }}</td>
                    <td>{{ ucwords($loan -> loan_type) }}</td>
                    <td>{{ ucwords($loan -> loan_purpose) }}</td>
                    <td>{{ ucwords($loan -> mortgage_type) }}</td>
                    <td>{{ $loan -> lender -> company_name ?? null }}</td>
                    <td>{{ $loan -> state }}</td>

                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="flex justify-end mt-2 pagination-div">
    {{ $loans -> onEachSide(1) -> links() }}
</div>
