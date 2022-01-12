@php
$title = 'Dashboard';
$breadcrumbs = [];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-24 lg:pb-36 pt-2"
    x-data="dashboard('{{ $group }}')">

        <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-12 pt-4 md:pt-8">

            <div class="">

                <div class="border-4 rounded-lg">

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

                    <div class="p-2 max-h-600-px overflow-auto whitespace-nowrap pb-96">

                        @forelse($active_loans as $loan)

                            @php
                            $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                            if($loan -> co_borrower_first != '') {
                                $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                            }
                            $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
                            @endphp

                            <div class="flex justify-start items-center p-2 mb-2 border-b text-sm">

                                <div class="w-20">
                                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View <i class="fal fa-arrow-right ml-2"></i></a>
                                </div>

                                <div class="w-20 flex justify-around">
                                    {!! App\Helpers\Helper::avatar($loan -> processor_id, 'mortgage') !!}
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
                                            } else if($incomplete == true) {
                                                echo '<span data-tippy-content="'.$text_incomplete.'"><i class="fal fa-times fa-2x text-red-600"></i></span>';
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

                    </div>

                </div>

                <div class="border-4 rounded-lg mt-12">

                    <div class="rounded-t-lg border-b p-3 text-lg font-semibold">
                        Recent Commissions
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

                            <div class="grid grid-cols-11 p-2 mb-2 border-b text-sm">

                                <div class="col-span-2">
                                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}?tab=commission" class="button primary md">View</a>
                                </div>
                                <div class="col-span-3 pl-2">
                                    {!! $borrower !!}
                                </div>
                                <div class="col-span-4 pl-2">
                                    {!! $address !!}
                                </div>
                                <div class="col-span-2 -pl-2">
                                    <div class="text-green-600">${{ number_format($loan -> loan_officer_1_commission_amount, 2) }}</div>
                                    <div class="text-xs">
                                        CD - {{ $loan -> settlement_date }}
                                    </div>
                                </div>

                            </div>

                        @empty

                            <div class="w-full px-4 py-12 text-gray-400 text-xl text-center">No Active Loans</div>

                        @endforelse

                    </div>

                </div>


                <div class="border-4 rounded-lg mt-12">

                    <div class="rounded-t-lg border-b p-3 text-lg font-semibold">
                        Software/Marketing
                    </div>

                    <div class="p-2">

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                            <div class="grid grid-rows-2 p-2 rounded bg-blue-50 text-center">

                                <div class="">
                                    <span class="font-semibold text-lg">Lending Pad</span><br>
                                    Loan Origination Software.
                                </div>
                                <div class="flex justify-around items-end">
                                    <a href="/heritage_financial/loan_software" class="button primary lg">Lending Pad Info <i class="fal fa-arrow-right ml-2"></i></a>
                                </div>

                            </div>

                            <div class="grid grid-rows-2 p-2 rounded bg-blue-50 text-center">

                                <div class="">
                                    <span class="font-semibold text-lg">Floify</span><br>
                                    Online application and document management system.
                                </div>
                                <div class="flex justify-around items-end">
                                    <a href="/heritage_financial/loan_software" class="button primary lg">Floify Info <i class="fal fa-arrow-right ml-2"></i></a>
                                </div>

                            </div>

                            @if(auth() -> user() -> level == 'loan_officer')
                            <div class="col-span-1 sm:col-span-2">

                                <div class="text-lg font-semibold mb-4">Your Marketing and Online Application Links</div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Profile Link</div>
                                    <div class="ml-4">
                                        <a href="https://heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder }}" target="_blank">heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder }}</a>
                                    </div>
                                </div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Floify Application Landing Page</div>
                                    <div class="ml-4">
                                        <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/</a>
                                    </div>
                                </div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Floify Start Application Page</div>
                                    <div class="ml-4">
                                        <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/apply-now" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/apply-now</a>
                                    </div>
                                </div>

                            </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
