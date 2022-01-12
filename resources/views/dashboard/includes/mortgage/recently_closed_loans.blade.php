<div class="rounded-t-lg border-b p-3 text-lg font-semibold">
    Recent @if(auth() -> user() -> level == 'loan_officer') Commissions @else Settlements @endif
</div>

<div class="p-2 max-h-400-px overflow-auto whitespace-nowrap">

    @forelse($recent_commissions as $loan)

        @php
        $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
        if($loan -> co_borrower_first != '') {
            $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
        }
        $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
        @endphp

        <div class="flex items-center justify-start p-2 mb-2 border-b text-sm">

            <div class="px-2">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}?tab=commission" class="button primary md">View</a>
            </div>
            <div class="w-48 px-2">
                {!! $borrower !!}
            </div>
            <div class="w-72 px-2 overflow-x-hidden">
                {!! $address !!}
            </div>
            <div class="w-32 px-2">
                CD - {{ $loan -> settlement_date }}
            </div>
            @if(auth() -> user() -> level == 'loan_officer')
                <div class="px-2 flex-grow text-right">
                    <div class="text-green-600">${{ number_format($loan -> loan_officer_1_commission_amount, 2) }}</div>
                </div>
            @else
                <div class="pl-4 flex-grow">
                    {{ $loan -> loan_officer_1 -> fullname }}
                </div>
            @endif

        </div>

    @empty

        <div class="w-full px-4 py-12 text-gray-400 text-xl text-center">No Active Loans</div>

    @endforelse

</div>
