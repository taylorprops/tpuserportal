<div class="p-2 border rounded bg-white">

    @foreach ($loans as $loan)

        <div class="grid grid-cols-7 m-2 p-2 border rounded-md">
            <div>
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View</a>
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
            <div>
                {{ $loan -> borrower_fullname }}
                @if($loan -> co_borrower_first)
                <br>
                {{ $loan -> co_borrower_fullname }}
                @endif
            </div>
            <div>
                ${{ number_format($loan -> loan_amount) }}
            </div>
        </div>

    @endforeach

</div>
