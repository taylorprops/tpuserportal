@foreach ($loans as $loan)

        <div class="grid grid-cols-5 sm:grid-cols-9 m-2 p-2 border rounded-md text-xs sm:text-sm">
            <div class="hidden sm:inline-block">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary sm w-24 mb-2">View</a>
                @if($loan -> lending_pad_uuid)
                <a href="https://prod.lendingpad.com/web/#/company/loans/{{ $loan -> lending_pad_uuid }}" target="_blank" class="button primary sm w-24">View on LP</a>
                @endif
            </div>
            <div class="inline-block sm:hidden">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}">View</a>
            </div>
            <div class="col-span-2 mr-4">
                {{ $loan -> borrower_fullname }}
                @if($loan -> co_borrower_first)
                <br>
                {{ $loan -> co_borrower_fullname }}
                @endif
            </div>
            <div class="col-span-2 mr-4">
                {!! $loan -> street.'<br>'.$loan -> city.', '.$loan -> state.' '.$loan -> zip !!}
            </div>
            <div class="text-right hidden sm:inline-block mr-4">
                ${{ number_format($loan -> loan_amount) }}
            </div>
            <div class="hidden sm:inline-block mr-4">
                {{ $loan -> loan_status }}
            </div>
            <div class="hidden sm:inline-block mr-4">
                Close Date<br>
                {{ $loan -> settlement_date }}
            </div>
            @if(auth() -> user() -> level != 'loan_officer')
            <div class="hidden sm:inline-block">
                {{ $loan -> loan_officer_1 -> fullname ?? null }}
            </div>
            @endif


        </div>

    @endforeach





{{-- @foreach ($loans as $loan)

        <div class="grid grid-cols-5 sm:grid-cols-7 m-2 p-2 border rounded-md text-xs sm:text-sm">
            <div class="hidden sm:inline-block">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary sm w-24 mb-2">View</a>
                @if($loan -> lending_pad_uuid)
                <a href="https://prod.lendingpad.com/web/#/company/loans/{{ $loan -> lending_pad_uuid }}" target="_blank" class="button primary sm w-24">View on LP</a>
                @endif
            </div>
            <div class="inline-block sm:hidden">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}">View</a>
            </div>
            <div class="hidden sm:inline-block">
                {{ $loan -> loan_status }}
            </div>
            <div class="col-span-2">
                {!! $loan -> street.'<br>'.$loan -> city.', '.$loan -> state.' '.$loan -> zip !!}
            </div>
            <div class="hidden sm:inline-block">
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
            <div class="text-right hidden sm:inline-block">
                ${{ number_format($loan -> loan_amount) }}
            </div>
        </div>

    @endforeach
 --}}
