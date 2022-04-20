<div class="p-4 rounded-md shadow bg-white min-w-1000-px">

    <div class="flex justify-end mr-4 mt-2">
        <a href="javascript:void(0)" id="close_search_results"><i class="fa fa-times text-red-600"></i></a>
    </div>

    <div>

        @foreach ($loans as $loan)

            <div class="grid sm:grid-cols-11 m-2 p-2 border rounded-md">
                <div class="flex items-center justify-around">
                    <a href="https://prod.lendingpad.com/web/#/company/loans/{{ $loan -> lending_pad_uuid }}" target="_blank" class="p-2 border rounded bg-blue-500 text-white">View</a>
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
                <div class="mr-4">
                    ${{ number_format($loan -> loan_amount) }}
                    <br>
                    {{ $loan -> loan_status }}
                </div>
                <div class="col-span-2 mr-4">
                    Loan #: {{ $loan -> loan_number }}<br>
                    LP ID: {{ $loan -> lending_pad_loan_number }}
                </div>
                <div class="mr-4">
                    CD<br>
                    {{ $loan -> settlement_date }}
                </div>
                <div class="col-span-2">
                    {{ $loan -> loan_officer_1 -> fullname ?? null }}
                </div>


            </div>

        @endforeach



    </div>

</div>
