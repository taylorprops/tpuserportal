<div class="p-2 border rounded bg-white">

    <div class="text-lg font-semibold text-gray-700 ml-4 mb-2">Loans</div>

    @foreach ($loans as $loan)

        <div class="grid grid-cols-5 sm:grid-cols-7 m-2 p-2 border rounded-md text-xs sm:text-sm">
            <div class="hidden sm:inline-block">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View</a>
            </div>
            <div class="inline-block sm:hidden">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}">View</a>
            </div>
            <div class="inline-block sm:hidden">
                {{ $loan -> loan_status }}
            </div>
            <div class="col-span-2">
                {!! $loan -> street.'<br>'.$loan -> city.', '.$loan -> state.' '.$loan -> zip !!}
            </div>
            <div class="inline-block sm:hidden">
                Close Date<br>
                {{ $loan -> settlement_date }}
            </div>
            <div>
                {{ $loan -> borrower_fullname }}
                @if($loan -> co_borrower_first)
                <br>
                {{ $loan -> co_borrower_fullname }}
                @endif
            </div>
            <div class="text-right inline-block sm:hidden">
                ${{ number_format($loan -> loan_amount) }}
            </div>
        </div>

    @endforeach

</div>
