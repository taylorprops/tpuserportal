

<div class="flex justify-between items-center rounded-t-lg border-b">

    <div class="flex items-center space-x-4">
        <div class="p-3 text-lg font-semibold">Active Loans</div>
        <div class="flex items-center justify-around h-10 w-10 p-1 font-semibold rounded-full overflow-hidden bg-yellow-600 text-white">
            {{ count($active_loans) }}
        </div>
    </div>

    <div class="mr-4">
        <a href="/heritage_financial/loans" class="button primary sm">View All</a>
    </div>

</div>

<div class="flex flex-col h-screen-70 w-screen-90 xl:w-screen-70 overflow-auto">

    <div class="-my-2 sm:-mx-6 lg:-mx-8">

        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">

            <div class="flex border-b">

                <div class="w-128 flex-none"></div>

                <div class="flex flex-none bg-gray-100">
                    @foreach($table_headers as $header)
                        <div class="w-8 h-40 whitespace-nowrap border-r border-gray-500">
                            <div class="transform rotate-270 translate-y-30 text-xs">
                                {{ $header['title'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <div class="pl-2 max-h-600-px overflow-auto whitespace-nowrap{{--  @if(count($active_loans) > 7) pb-96 @endif --}}">

                @forelse($active_loans as $loan)

                    @php
                    $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                    if($loan -> co_borrower_first != '') {
                        $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                    }
                    $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;

                    $close_date_type = 'Estimated';
                    $close_date_details = 'ECD ';
                    $close_date_details .= $loan -> time_line_estimated_settlement ? date('n/j/Y', strtotime($loan -> time_line_estimated_settlement )) : '---';
                    $class = 'text-blue-500';
                    if($loan -> time_line_scheduled_settlement != '') {
                        $close_date_type = 'Scheduled';
                        $close_date_details = 'SCD  '.date('n/j/Y', strtotime($loan -> time_line_scheduled_settlement));
                        $class = 'text-green-500';
                    }
                    @endphp

                    <div class="flex justify-start items-center p-2 border-b hover:bg-gray-50">

                        <div class="w-20 flex-none">
                            <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View <i class="fal fa-arrow-right ml-2"></i></a>
                        </div>

                        <div class="w-20 flex-none flex justify-around">
                            {!! App\Helpers\Helper::avatar(null, $loan -> processor_id, 'mortgage') !!}
                        </div>

                        <div class="w-52 text-sm">
                            <div class="font-semibold text-gray-700">{!! $borrower !!}</div>
                            <div class="text-xs">{!! $address !!}</div>
                        </div>

                        <div class="w-32 flex-none">
                            <div class="text-xs">
                                ${{ number_format($loan -> loan_amount) }}
                            </div>
                            <div class="text-xs {{ $class }}" title="{{ $close_date_type }} Close Date">
                                {{ $close_date_details }}
                            </div>
                            <div class="text-xs">
                                {{ $loan -> loan_officer_1 -> fullname }}
                            </div>
                        </div>

                        <div class="flex flex-none">

                            @foreach($table_headers as $header)

                                <div class="flex items-center justify-around w-8 border-r border-gray-400">

                                    @php
                                    $field = $header['db_field'];

                                    $complete = false;
                                    $incomplete = false;
                                    $not_available = false;
                                    $suspended = false;

                                    if($field == 'locked') {

                                        if($loan -> locked == 'Locked') {
                                            $complete = true;
                                        } else {
                                            $incomplete = true;
                                        }

                                        $text_complete = 'Yes<br>Expires: '.date('n/j/Y', strtotime($loan -> lock_expiration));
                                        $text_incomplete = $loan -> locked ?? 'No';

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

                                        $text_complete = 'Completed<br>'.date('n/j/Y', strtotime($loan -> $field));
                                        $text_incomplete = 'Not Completed';

                                        if($field == 'time_line_scheduled_settlement') {
                                            $text_complete = 'Scheduled<br>'.date('n/j/Y', strtotime($loan -> $field));
                                            $text_incomplete = 'Not Scheduled';
                                        }

                                    }


                                    if($complete == true) {
                                        echo '<span data-tippy-content="'.$text_complete.'"><i class="fal fa-check text-green-600"></i></span>';
                                    } else if($incomplete == true && $suspended == false) {
                                        echo '<span data-tippy-content="'.$text_incomplete.'"><i class="fal fa-times text-red-600"></i></span>';
                                    } else if($incomplete == true && $suspended == true) {
                                        echo '<span data-tippy-content="'.$text_incomplete.'"><i class="fal fa-exclamation-circle text-red-600"></i></span>';
                                    } else if($not_available == true) {
                                        echo '<span data-tippy-content="N/A"><i class="fal fa-minus text-gray-300"></i></span>';
                                    }
                                    @endphp

                                </div>

                            @endforeach
                        </div>

                    </div>

                @empty

                    <div class="w-full px-4 py-12 text-gray-400 text-xl text-center">No Active Loans</div>

                @endforelse

            </div>

             @if(count($active_loans) > 7)

                <div class="flex border-b">

                    <div class="w-128 flex-none"></div>

                    <div class="flex bg-gray-100">
                        @foreach($table_headers as $header)
                            <div class="w-8 h-40 whitespace-nowrap border-r border-gray-500">
                                <div class="transform rotate-90 translate-y-5 text-xs">
                                    {{ $header['title'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            @endif


        </div>

    </div>

</div>


