<div class="p-2 border rounded bg-white">

    <div class="text-lg font-semibold text-gray-700 ml-4 mb-2">Loans</div>

    @foreach ($loans as $loan)

        <div class="grid grid-cols-9 m-2 p-2 border rounded-md text-sm">
            <div>
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View</a>
            </div>
            <div>
                {{ $loan -> loan_officer_1 -> fullname }}
            </div>
            <div>
                {{ $loan -> loan_status }}
            </div>
            <div class="col-span-2">
                {!! $loan -> street.'<br>'.$loan -> city.', '.$loan -> state.' '.$loan -> zip !!}
            </div>
            <div>
                Close Date<br>
                {{ $loan -> settlement_date }}
            </div>
            <div class="col-span-2">
                {{ $loan -> borrower_fullname }}
                @if($loan -> co_borrower_first)
                <br>
                {{ $loan -> co_borrower_fullname }}
                @endif
            </div>
            <div class="text-right">
                ${{ number_format($loan -> loan_amount) }}
            </div>
        </div>

    @endforeach

</div>
