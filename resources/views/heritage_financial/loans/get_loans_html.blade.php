<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $loans -> onEachSide(1) -> links() }}
</div>

<div class="shadow border-b border-gray-200 sm:rounded-lg">

    <table class="w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr id="sortable_tr">
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('loan_status', 'Status')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('loan_officer_last', 'Loan Officer')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('street', 'Address')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('settlement_date', 'Close Date')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('borrower_last', 'Borrower')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('loan_amount', 'Loan Amount')</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($loans as $loan)
                @php
                $address = $loan -> street.' '.$loan -> city.', '.$loan -> state.' '.$loan -> zip;
                $loan_officer = $loan -> loan_officer_last.', '.$loan -> loan_officer_first;
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="view-link button primary md" target="_blank">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $loan -> loan_status }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $loan_officer }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $loan -> settlement_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        {{ $loan -> borrower_last.', '.$loan -> borrower_first }}
                        @if($loan -> co_borrower_last != '')
                            <br>
                            {{ $loan -> co_borrower_last.', '.$loan -> co_borrower_first }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">${{ number_format($loan -> loan_amount) }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $loans -> onEachSide(1) -> links() }}
</div>
