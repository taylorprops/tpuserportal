

<div class="flex justify-between items-center rounded-t-lg border-b">
    <div class="p-3 text-lg font-semibold">Active Loans</div>
    <div class="mr-4">
        <a href="/heritage_financial/loans" class="button primary sm">View All</a>
    </div>
</div>

<div class="flex border-b">

    <div class="w-128"></div>

    <div class="flex bg-gray-100">
        @foreach($table_headers as $header)
            <div class="w-12 h-40 whitespace-nowrap border-r border-gray-500">
                <div class="transform rotate-270 translate-y-30 text-sm">
                    {{ $header['title'] }}
                </div>
            </div>
        @endforeach
    </div>

</div>

<div class="p-2 pt-0 h-auto max-h-600-px overflow-auto whitespace-nowrap{{--  @if(count($active_loans) > 7) pb-96 @endif --}}">

    @forelse($active_loans as $loan)

        @php
        $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
        if($loan -> co_borrower_first != '') {
            $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
        }
        $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
        @endphp

        <div class="flex justify-start items-center p-2 border-b text-sm hover:bg-gray-50">

            <div class="w-20">
                <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View <i class="fal fa-arrow-right ml-2"></i></a>
            </div>

            <div class="w-20 flex justify-around">
                {!! App\Helpers\Helper::avatar(null, $loan -> processor_id, 'mortgage') !!}
            </div>

            <div class="w-52">
                <div class="font-semibold text-gray-700">{!! $borrower !!}</div>
                <div class="text-xs">{!! $address !!}</div>
            </div>

            <div class="w-32">
                ${{ number_format($loan -> loan_amount) }}
                <div class="text-xs">
                    CD - {{ $loan -> settlement_date }}
                </div>
            </div>

            <div class="flex">

                @foreach($table_headers as $header)

                    <div class="flex items-center justify-around w-12 border-r border-gray-400">

                        @php
                        $field = $header['db_field'];

                        $complete = false;
                        $incomplete = false;
                        $not_available = false;
                        $suspended = false;

                        if($field == 'locked') {

                            if($loan -> locked == 'yes') {
                                $complete = true;
                            } else {
                                $incomplete = true;
                            }

                            $text_complete = 'Yes<br>Expires: '.$loan -> lock_expiration;
                            $text_incomplete = 'No';

                        } elseif($field == 'time_line_conditions_received_status') {

                            if($loan -> time_line_conditions_received_status == 'approved') {
                                $complete = true;
                            } elseif($loan -> time_line_conditions_received_status == 'suspended') {
                                $incomplete = true;
                                $suspended = true;
                            } else {
                                $not_available = true;
                            }

                            $text_complete = 'Approved';
                            $text_incomplete = 'Suspended';

                        } else {

                            if($loan -> $field) {
                                $complete = true;
                            } else {
                                $incomplete = true;
                            }

                            $text_complete = 'Completed<br>'.$loan -> $field;
                            $text_incomplete = 'Not Completed';

                        }


                        if($complete == true) {
                            echo '<span data-tippy-content="'.$text_complete.'"><i class="fal fa-check fa-2x text-green-600"></i></span>';
                        } else if($incomplete == true && $suspended == false) {
                            echo '<span data-tippy-content="'.$text_incomplete.'"><i class="fal fa-times fa-2x text-red-600"></i></span>';
                        } else if($incomplete == true && $suspended == true) {
                            echo '<span data-tippy-content="'.$text_incomplete.'"><i class="fal fa-exclamation-circle fa-2x text-red-600"></i></span>';
                        } else if($not_available == true) {
                            echo '<span data-tippy-content="N/A"><i class="fal fa-minus fa-2x text-gray-300"></i></span>';
                        }
                        @endphp

                    </div>

                @endforeach
            </div>

        </div>

    @empty

        <div class="w-full px-4 py-12 text-gray-400 text-xl text-center">No Active Loans</div>

    @endforelse

    @if(count($active_loans) > 7)

        <div class="flex border-b">

            <div class="w-128"></div>

            <div class="flex bg-gray-100">
                @foreach($table_headers as $header)
                    <div class="w-12 h-40 whitespace-nowrap border-r border-gray-500">
                        <div class="transform rotate-90 translate-y-5 text-sm">
                            {{ $header['title'] }}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    @endif

</div>

