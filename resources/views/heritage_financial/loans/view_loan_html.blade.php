@php
$title = 'Add Loan';
if($loan) {
    $title = $loan -> street;
}
$breadcrumbs = [
    ['Heritage Financial', ''],
    ['Loans', '/heritage_financial/loans'],
    [$title, ''],
];
$input_size = 'md';

$active_tab = '1';
if(isset($_GET['tab']) && $_GET['tab'] == 'commission') {
    $active_tab = '2';
}
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-48 pt-2"
    x-data="loan(
        '{{ $active_tab }}',
        '{{ $loan_officer_1_commission_type }}',
        '{{ $loan_officer_2_commission_type ?? 'commission' }}',
        '{{ $loan -> loan_amount ?? null }}',
        '{{ $loan -> points_charged ?? '2.5' }}',
        '{{ $manager_bonus }}',
        '{{ $loan_officer_1 -> loan_amount_percent ?? null }}',
        '{{ $loan_officer_2 -> loan_amount_percent ?? null }}')">

        <div class="max-w-1400-px mx-auto pt-8 md:pt-12 lg:pt-16 px-4">


            <div class="sm:hidden">
                @if($loan)
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full focus:ring-primary focus:border-primary border-gray-300 rounded-md"
                @change="active_tab = $el.value">
                    <option selected value="1">Details</option>
                    <option value="2">Commission</option>
                    <option value="3">Documents</option>
                </select>
                @endif
            </div>

            <div class="hidden sm:block">

                <div class="border-b border-gray-200">

                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '1', 'border-primary text-primary-dark': active_tab === '1' }"
                        @click="active_tab = '1'">
                            <i class="fad fa-calendar-day mr-3"
                            :class="{ 'text-primary': active_tab === '1', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '1' }"></i>
                            <span>Details</span>
                        </a>

                        @if($loan)
                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2', 'border-primary text-primary-dark': active_tab === '2' }"
                        @click="active_tab = '2'">
                            <i class="fad fa-calculator mr-3"
                            :class="{ 'text-primary': active_tab === '2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2' }"></i>
                            <span>Commission</span>
                        </a>

                        <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                        :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3', 'border-primary text-primary-dark': active_tab === '3' }"
                        @click="active_tab = '3'">
                            <i class="fad fa-copy mr-3"
                            :class="{ 'text-primary': active_tab === '3', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3' }"></i>
                            <span>Documents</span>
                        </a>

                        @endif
                    </nav>

                </div>

            </div>

            <div>

                <div x-show="active_tab === '1'" class="pt-4 sm:pt-12 max-w-1000-px">

                    @if(auth() -> user() -> group != 'mortgage')

                        <form id="details_form">

                            <div class="font-medium text-gray-700 border-b mb-2"><i class="fad fa-users mr-2"></i> People</div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    id="loan_officer_1_id"
                                    name="loan_officer_1_id"
                                    data-label="Loan Officer">
                                        <option value=""></option>
                                        @foreach($loan_officers -> where('emp_position', 'loan_officer') as $lo)
                                        <option value="{{ $lo -> id }}" @if($loan && $loan -> loan_officer_1_id == $lo -> id) selected @endif>{{ $lo -> last_name }}, {{ $lo -> first_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }}"
                                    id="loan_officer_2_id"
                                    name="loan_officer_2_id"
                                    data-label="Loan Officer 2">
                                        <option value=""></option>
                                        @foreach($loan_officers -> where('emp_position', 'loan_officer') as $lo)
                                        <option value="{{ $lo -> id }}" @if($loan && $loan -> loan_officer_2_id == $lo -> id) selected @endif>{{ $lo -> last_name }}, {{ $lo -> first_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    id="processor_id"
                                    name="processor_id"
                                    data-label="Processor">
                                        <option value=""></option>
                                        @foreach($loan_officers -> whereIn('emp_position', ['processor', 'manager']) as $lo)
                                        <option value="{{ $lo -> id }}" @if($loan && $loan -> processor_id == $lo -> id) selected @endif>{{ $lo -> last_name }}, {{ $lo -> first_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="agent_name"
                                    name="agent_name"
                                    data-label="Agent Name"
                                    value="{{ $loan -> agent_name ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="agent_company"
                                    company="agent_company"
                                    data-label="Agent Company"
                                    value="{{ $loan -> agent_name ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="borrower_first"
                                    name="borrower_first"
                                    data-label="Borrower First"
                                    value="{{ $loan -> borrower_first ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="borrower_last"
                                    name="borrower_last"
                                    data-label="Borrower Last"
                                    value="{{ $loan -> borrower_last ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="co_borrower_first"
                                    name="co_borrower_first"
                                    data-label="Co-Borrower First"
                                    value="{{ $loan -> co_borrower_first ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="co_borrower_last"
                                    name="co_borrower_last"
                                    data-label="Co-Borrower Last"
                                    value="{{ $loan -> co_borrower_last ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    name="title_company_select"
                                    id="title_company_select"
                                    data-label="Title Company"
                                    @change="if($el.value != 'other') { $refs.title_company.value = $el.value } else { $refs.title_company.value = '' }">
                                        <option value=""></option>
                                        <option value="Heritage Title" @if($loan && $loan -> title_company == 'Heritage Title') selected @endif>Heritage Title</option>
                                        <option value="Title Nation" @if($loan && $loan -> title_company == 'Title Nation') selected @endif>Title Nation</option>
                                        <option value="other" @if($loan && $loan -> title_company != 'Heritage Title' && $loan -> title_company != 'Title Nation') selected @endif>Other</option>
                                    </select>
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="title_company"
                                    name="title_company"
                                    data-label="Title Company Name"
                                    value="{{ $loan -> title_company ?? null }}"
                                    x-ref="title_company">
                                </div>

                            </div>

                            <div class="font-medium text-gray-700 border-b mb-2 mt-8"><i class="fad fa-home-alt mr-2"></i> Property Address</div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-8">

                                <div class="m-2 sm:m-3 sm:col-span-2 lg:col-span-4">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="street"
                                    name="street"
                                    data-label="Street"
                                    value="{{ $loan -> street ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="zip"
                                    name="zip"
                                    data-label="Zip"
                                    value="{{ $loan -> zip ?? null }}"
                                    x-on:keyup="get_location_details('#details_form', '', '#zip', '#city', '#state');">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1 lg:col-span-2">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="city"
                                    name="city"
                                    data-label="City"
                                    value="{{ $loan -> city ?? null }}">
                                </div>

                                <div class="m-2 sm:m-3 col-span-1">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    id="state"
                                    name="state"
                                    data-label="State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            <option value="{{ $state -> state }}" @if($loan && $loan -> state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="font-medium text-gray-700 border-b mb-2 mt-8"><i class="fad fa-calendar-day mr-2"></i> Loan Details</div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    id="loan_status"
                                    name="loan_status"
                                    data-label="Loan Status">
                                        <option value=""></option>
                                        <option value="Open" @if($loan && $loan -> loan_status == 'Open') selected @endif>Open</option>
                                        <option value="Closed" @if($loan && $loan -> loan_status == 'Closed') selected @endif>Closed</option>
                                        <option value="Cancelled" @if($loan && $loan -> loan_status == 'Cancelled') selected @endif>Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="date"
                                    class="form-element input {{ $input_size }} required"
                                    id="settlement_date"
                                    name="settlement_date"
                                    data-label="Settlement Date"
                                    value="{{ $loan -> settlement_date ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} required"
                                    id="loan_number"
                                    name="loan_number"
                                    data-label="Loan Number"
                                    value="{{ $loan -> loan_number ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} numbers-only money-decimal required"
                                    id="loan_amount"
                                    name="loan_amount"
                                    data-label="Loan Amount"
                                    value="{{ $loan -> loan_amount ?? null }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} text-center numbers-only required"
                                    id="points_charged"
                                    name="points_charged"
                                    data-label="Points Charged"
                                    value="{{ $loan -> points_charged ?? '0.00' }}">
                                </div>

                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }} required"
                                    id="source"
                                    name="source"
                                    data-label="Loan Source">
                                        <option value=""></option>
                                        <option value="Office" @if($loan && $loan -> source == 'Office') selected @endif>Office</option>
                                        <option value="Loan Officer" @if($loan && $loan -> source == 'Loan Officer') selected @endif>Loan Officer</option>
                                    </select>
                                </div>

                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="p-8 flex justify-around">
                                <button type="button" class="button primary xl" @click="save_details($el)"><i class="fal fa-check mr-3"></i> Save Details</button>
                            </div>

                            <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                        </form>

                    @else

                        <div class="font-medium text-2xl text-gray-600 mt-2">{{ $loan -> street.' '.$loan -> city.', '.$loan -> state.' '.$loan -> zip }}</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">

                            <div class="col-span-1">

                                <div class="border-4 rounded-lg">

                                    <div class="p-4 font-medium text-gray-700 border-b"><i class="fad fa-calendar-day mr-2"></i> Loan Details</div>



                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Status
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> loan_status }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Settlement Date
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> settlement_date }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Loan Number
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> loan_number }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Loan Amount
                                        </div>
                                        <div class="font-bold col-span-2">
                                            ${{ number_format($loan -> loan_amount) }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Points Charged
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> points_charged }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Source
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> source }}
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="col-span-1">

                                <div class="border-4 rounded-lg">

                                    <div class="p-4 font-medium text-gray-700 border-b"><i class="fad fa-users mr-2"></i> People</div>

                                    @if($loan_officer_2)
                                        <div class="grid grid-cols-3 gap-4 p-4">
                                            <div class="text-right">
                                                Loan Officer 2
                                            </div>
                                            <div class="font-bold col-span-2">
                                                {{ $loan_officer_2 -> fullname }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Processor
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $processor -> fullname }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Borrower 1
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> borrower_fullname }}
                                        </div>
                                    </div>

                                    @if($loan -> co_borrower_fullname != '')
                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Borrower 2
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> co_borrower_fullname }}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="grid grid-cols-3 gap-4 p-4">
                                        <div class="text-right">
                                            Title Company
                                        </div>
                                        <div class="font-bold col-span-2">
                                            {{ $loan -> title_company }}
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>


                <div x-show="active_tab === '2'" class="pt-4 sm:pt-12">


                    <div class="mt-4 max-w-lg @if(auth() -> user() -> group != 'mortgage') hidden @endif">

                        <div class="grid grid-cols-2 border-4 p-2 rounded-lg mb-3">

                            <div>
                                Checks In
                            </div>

                            <div class="font-bold text-right">
                                <span class="checks-in-amount text-green-600"></span>
                            </div>

                        </div>

                        <div class="border-4 p-2 rounded-lg mb-3">

                            <div>
                                Deductions
                            </div>

                            <div class="mt-4">
                                <div class="w-3/4 deductions-checks-in text-sm border p-2 rounded-xl"></div>
                            </div>

                            <div class="grid grid-cols-2 mt-4">
                                <div>Total Deductions</div>
                                <div class="font-bold text-right text-red-600">
                                    <span>-</span> <span class="deductions-total"></span>
                                </div>
                            </div>

                        </div>

                        <div class="grid grid-cols-2 border-4 p-2 rounded-lg mb-3 mt-8">

                            <div>
                                Net Commission
                            </div>

                            <div class="font-bold text-right text-green-600">
                                <span>=</span> <span class="net-commission-amount"></span>
                            </div>

                        </div>

                        <div class="border-4 border-green-200 p-4 rounded-lg mb-3 mt-8">

                            <div class="text-xl">
                                Loan Officer Commission
                            </div>

                            <div class="mt-4">
                                <div class="loan-officer-1-loan-amount-details"></div>
                            </div>

                            @if($loan_officer_deductions && count($loan_officer_deductions -> where('lo_index', '1')) > 0)

                                <div class="mt-4">
                                    <div class="deductions-from-lo text-xs border bg-white rounded-xl">

                                        <div class="p-2 border-b">Deductions</div>

                                        @php $total_deductions = 0; @endphp

                                        @foreach($loan_officer_deductions -> where('lo_index', '1') as $loan_officer_deduction)
                                            @php $total_deductions += $loan_officer_deduction -> amount; @endphp
                                            <div class="grid grid-cols-2 p-2">
                                                <div class="">{{ $loan_officer_deduction -> description }}</div>
                                                <div class="text-right">${{ number_format($loan_officer_deduction -> amount, 2) }}</div>
                                            </div>
                                        @endforeach

                                        <hr>

                                        <div class="grid grid-cols-2 p-2">
                                            <div class="font-semibold">Total Deductions</div>
                                            <div class="font-semibold text-right text-red-600">- ${{ number_format($total_deductions, 2) }}</div>
                                        </div>

                                    </div>

                                </div>

                            @endif

                            <div class="grid grid-cols-2 mt-6">

                                <div class="font-bold text-xl">Total Commission</div>

                                <div class="font-bold text-right text-xl">
                                    <span class="loan-officer-1-commission-amount-ele text-green-600"></span>
                                </div>

                            </div>

                        </div>

                    </div>


                    <div @if(auth() -> user() -> group == 'mortgage') class="hidden" @endif>

                        <form id="commission_form">


                            <div class="grid grid-cols-7 max-w-1200-px">

                                <div class="col-span-7 lg:col-span-5">


                                    <div class="flex justify-start items-center">

                                        <div class="font-medium text-xl">Checks In</div>

                                        <button type="button" class="button primary sm no-text ml-3"
                                        @click="add_check_in()">
                                            <i class="fal fa-plus"></i>
                                        </button>

                                    </div>

                                    <div class="grid grid-cols-5 mt-4 gap-8">

                                        <div class="col-span-4 grid grid-cols-4 checks-in">

                                            @forelse($checks_in as $check_in)

                                                <div class="flex justify-start items-end mt-2 check-in">

                                                    <div class="w-36">
                                                        <input
                                                        type="text"
                                                        class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                                                        name="check_in_amount[]"
                                                        data-label="Check Amount"
                                                        value="{{ $check_in -> amount }}"
                                                        @keyup="set_checks_in_amount();">
                                                    </div>

                                                    <div class="mx-2 mb-1">
                                                        <button type="button" class="button danger md no-text delete-check-in-button"
                                                        @click="$el.closest('.check-in').remove(); total_commission(); run_show_delete_check_in();"
                                                        x-show="show_delete_check_in">
                                                            <i class="fal fa-times"></i>
                                                        </button>
                                                    </div>

                                                </div>

                                            @empty

                                                <div class="flex justify-start items-end mt-2 check-in">

                                                    <div class="w-36">
                                                        <input
                                                        type="text"
                                                        class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                                                        name="check_in_amount[]"
                                                        data-label="Check Amount"
                                                        @keyup="set_checks_in_amount();">
                                                    </div>

                                                    <div class="mx-2 mb-1">
                                                        <button type="button" class="button danger md no-text delete-check-in-button"
                                                        @click="$el.closest('.check-in').remove(); total_commission(); run_show_delete_check_in();"
                                                        x-show="show_delete_check_in">
                                                            <i class="fal fa-times"></i>
                                                        </button>
                                                    </div>

                                                </div>

                                            @endforelse

                                        </div>

                                        <div class="col-span-1 ml-7 whitespace-nowrap place-self-end w-full">

                                            <div class="text-lg text-right bg-green-50 text-green-600 p-4 rounded-md">
                                                <div class="text-xs">Checks In</div>
                                                <span class="checks-in-amount">$0.00</span>
                                            </div>

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6">


                                    <div class="flex justify-start items-center mt-6">

                                        <div class="font-medium text-xl">Deductions</div>

                                        <button type="button" class="button primary sm no-text ml-3"
                                        @click="add_deduction(); if($refs.no_deductions) { $refs.no_deductions.remove() }">
                                            <i class="fal fa-plus"></i>
                                        </button>

                                    </div>

                                    <div class="grid grid-cols-5 gap-8 mt-4">

                                        <div class="col-span-4">

                                            <div class="deductions h-full">

                                                @forelse($deductions as $deduction)

                                                    @php
                                                    $non_other = ['Company', 'Loan Officer 1', 'Loan Officer 2'];
                                                    $show_other = null;
                                                    if(!in_array($deduction -> paid_to, $non_other)) {
                                                        $show_other = 'yes';
                                                    }
                                                    @endphp

                                                    <div class="flex justify-between items-end deduction">

                                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-9"
                                                        x-data="{ show_other: @if($show_other) true @else false @endif }">

                                                            <div class="col-span-2 mr-2 mb-2">
                                                                <input
                                                                type="text"
                                                                class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                                                                name="amount[]"
                                                                data-label="Amount"
                                                                value="{{ $deduction -> amount }}">
                                                            </div>

                                                            <div class="col-span-1 sm:col-span-2 md:col-span-3 mr-2 mb-2">
                                                                <input
                                                                type="text"
                                                                class="form-element input {{ $input_size }} commission-input required"
                                                                name="description[]"
                                                                data-label="Description"
                                                                value="{{ $deduction -> description }}">
                                                            </div>

                                                            <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                                                                <select
                                                                class="form-element select {{ $input_size }} required"
                                                                name="paid_to[]"
                                                                data-label="Paid To"
                                                                @change="show_other = false; if($el.value == 'Other') { show_other = true }; total_commission();">
                                                                    <option value=""></option>
                                                                    <option value="Company" @if($deduction -> paid_to == 'Company') selected @endif>Company</option>
                                                                    <option value="Loan Officer 1" @if($deduction -> paid_to == 'Loan Officer 1') selected @endif>{{ $loan_officer_1 -> fullname ?? 'Loan Officer 1' }}</option>
                                                                    @if($loan_officer_2)
                                                                    <option value="Loan Officer 2" @if($deduction -> paid_to == 'Loan Officer 2') selected @endif>{{ $loan_officer_2 -> fullname ?? 'Loan Officer 2' }}</option>
                                                                    @endif
                                                                    <option value="Other" @if($show_other) selected @endif>Other</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                                                                <div x-show="show_other">
                                                                    <input
                                                                    type="text"
                                                                    class="form-element input {{ $input_size }} commission-input required"
                                                                    name="paid_to_other[]"
                                                                    data-label="Paid To Name"
                                                                    value="{{ $deduction -> paid_to }}">
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="flex items-end mb-2 ml-3">
                                                            <button type="button" class="button danger md no-text"
                                                            @click.prevent="$el.closest('.deduction').remove(); total_commission();">
                                                                <i class="fal fa-times"></i>
                                                            </button>
                                                        </div>

                                                    </div>

                                                @empty

                                                    <div class="flex items-center text-gray-400 h-full" x-ref="no_deductions"><i class="fad fa-minus-circle mr-2"></i> No Deductions Added</div>

                                                @endforelse

                                            </div>

                                        </div>

                                        <div class="col-span-1 ml-7 whitespace-nowrap place-self-end w-full">

                                            <div class="text-lg text-right bg-red-50 text-red-600 p-4 rounded-md">
                                                <div class="text-xs">Deductions</div>
                                                <span class="deductions-total">$0.00</span>
                                            </div>

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6">

                                    <div class="grid grid-cols-5">

                                        <div class="col-span-4 flex items-center font-medium text-xl">Net Commission</div>

                                        <div class="col-span-1 ml-8 whitespace-nowrap">

                                            <div class="text-right bg-green-50 text-green-600 p-4 rounded-md">
                                                <div class="text-xs">Net Commisison</div>
                                                <span class="net-commission-amount">$0.00</span>
                                            </div>

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6">

                                    <div class="font-medium text-xl">Commissions Out</div>

                                    <div class="grid grid-cols-5 mt-6 gap-8">

                                        <div class="col-span-4">


                                            @php
                                            $los = ['1'];
                                            if($loan_officer_2) {
                                                $los[] = '2';
                                            }
                                            @endphp

                                            @foreach($los as $index)

                                                @php
                                                if($index == '1') {
                                                    $loan_officer = $loan_officer_1;
                                                    $loan_officer_active_commission_tab = $loan_officer_1_active_commission_tab;
                                                } else if($index == '2') {
                                                    $loan_officer = $loan_officer_2;
                                                    $loan_officer_active_commission_tab = $loan_officer_2_active_commission_tab;
                                                }
                                                @endphp

                                                <div class="p-4 mb-8 border rounded-md" id="loan_officer_{{ $index }}_commission"
                                                x-data="{
                                                    active_commission_tab: '{{ $loan_officer_active_commission_tab }}',
                                                    show_details: false,
                                                    index: '{{ $index }}'
                                                }">

                                                    <div class="flex justify-between items-center">

                                                        <div class="flex jusify-start items-center cursor-pointer w-full" @click="show_details = !show_details">

                                                            <div class="mr-6">
                                                                <a href="javascript:void(0)" class="block">
                                                                    <i :class="{ 'fas fa-plus-circle fa-lg text-yellow-600': show_details === false, 'fas fa-times-circle fa-lg text-red-600': show_details === true }"></i>
                                                                </a>
                                                            </div>

                                                            <div class="flex items-center w-96">
                                                                <div class="flex-1 text-lg text-gray-800 cursor-pointer">
                                                                    {{ $loan_officer -> fullname ?? 'Loan Officer' }}
                                                                </div>
                                                                @if($loan_officer)
                                                                    <div class="flex-1 text-gray-400">Loan Officer</div>
                                                                @endif
                                                            </div>

                                                        </div>

                                                        <div class="col-span-1 whitespace-nowrap">

                                                            <div class="text-right bg-primary-lightest text-primary-dark p-2 rounded-md">
                                                                <span class="loan-officer-{{ $index }}-commission-amount-ele">$0.00</span>
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div x-show="show_details" x-transition>

                                                        <nav class="flex space-x-4 my-4 items-center border-t pt-3" aria-label="Tabs">

                                                            <div class="mr-6">Calculate By</div>

                                                            <a href="javascript:void(0)" class=" px-3 py-2 font-medium text-sm rounded-md"
                                                            @click="active_commission_tab = '1';
                                                            if(index == '1') {
                                                                loan_officer_1_commission_type = 'commission';
                                                            } else if(index == '2') {
                                                                loan_officer_2_commission_type = 'commission';
                                                            }
                                                            document.querySelector('#loan_officer_'+index+'_commission_type').value = 'commission';
                                                            total_commission();"
                                                            :class="{ 'bg-primary-lightest text-primary-dark': active_commission_tab === '1', 'text-gray-500 hover:text-gray-700': active_commission_tab !== '1' }">
                                                                Commission
                                                            </a>

                                                            @if($loan_officer && $loan_officer -> loan_amount_percent == '0')
                                                            <a class="text-gray-400 line-through">Loan Amount</a> <a href="/employees/loan_officer/loan_officer_view/{{ $loan_officer -> id }}" target="_blank" class="text-xs">Edit LO to Enable</a>
                                                            @else
                                                            <a href="javascript:void(0)" class="px-3 py-2 font-medium text-sm rounded-md"
                                                            @click="active_commission_tab = '2';
                                                            if(index == '1') {
                                                                loan_officer_1_commission_type = 'loan_amount';
                                                            } else if(index == '2') {
                                                                loan_officer_2_commission_type = 'loan_amount';
                                                            }
                                                            document.querySelector('#loan_officer_'+index+'_commission_type').value = 'loan_amount';
                                                            total_commission()"
                                                            :class="{ 'bg-primary-lightest text-primary-dark': active_commission_tab === '2', 'text-gray-500 hover:text-gray-700': active_commission_tab !== '2' }">
                                                                Loan Amount
                                                            </a>
                                                            @endif
                                                        </nav>


                                                        <div class="mt-6">

                                                            <div x-show="active_commission_tab === '1'">

                                                                <div class="bg-primary-lightest p-3 rounded-xl">

                                                                    <div class="text-sm mb-2">Commission Percent</div>

                                                                    <div class="flex justify-start items-center">

                                                                        @if($loan_officer)
                                                                        <div class="w-24">
                                                                            @php
                                                                            $commission_percent = $loan_officer -> commission_percent;
                                                                            if($index == '1') {
                                                                                if($loan -> loan_officer_1_commission_percent) {
                                                                                    $commission_percent = $loan -> loan_officer_1_commission_percent;
                                                                                }
                                                                            } else if($index == '2') {
                                                                                if($loan -> loan_officer_2_commission_percent && $loan -> loan_officer_2_commission_percent > 0) {
                                                                                    $commission_percent = $loan -> loan_officer_2_commission_percent;
                                                                                }
                                                                            }
                                                                            //$commission_percent = rtrim(rtrim($commission_percent, '0'), '.');
                                                                            $commission_percent = $commission_percent + 0;
                                                                            @endphp
                                                                            <input
                                                                            type="text"
                                                                            class="form-element input {{ $input_size }} numbers-only text-center commission-input required"
                                                                            name="loan_officer_{{ $index }}_commission_percent"
                                                                            data-label=""
                                                                            value="{{ $commission_percent }}">


                                                                        </div>
                                                                        @endif

                                                                        <div><i class="fal fa-percentage ml-1 fa-lg text-gray-500"></i></div>


                                                                        <div class="ml-4">
                                                                            of
                                                                        </div>

                                                                        <div class="ml-4">

                                                                            @if($index == '1')

                                                                                <span class="net-commission-amount ml-3"></span>

                                                                            @elseif($index == '2')

                                                                                <span class="net-commission-amount ml-3"></span>

                                                                            @endif

                                                                        </div>

                                                                        <div class="ml-4">
                                                                            = <span class="loan-officer-{{ $index }}-commission-amount ml-3 text-lg font-bold"></span>
                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div x-show="active_commission_tab === '2'">

                                                                <div class="flex justify-start items-center mb-3 bg-primary-lightest text-primary-dark p-2 rounded-md"
                                                                x-show="show_alert">
                                                                    <div id="loan_officer_{{ $index }}_loan_amount_alert_icon"></div>
                                                                    <div id="loan_officer_{{ $index }}_loan_amount_alert_text"></div>
                                                                </div>

                                                                <div class="flex justify-start items-center bg-primary-lightest p-3 rounded-xl">
                                                                    <div class="loan-officer-{{ $index }}-loan-amount-details"></div>
                                                                </div>

                                                            </div>

                                                            @if($loan_officer)
                                                                @php
                                                                $loan_amount_percent = $loan_officer -> loan_amount_percent;

                                                                if($index == '1') {
                                                                    if($loan -> loan_officer_1_loan_amount_percent) {
                                                                        $loan_amount_percent = $loan -> loan_officer_1_loan_amount_percent;
                                                                    }
                                                                    $loan_officer_commission_type = $loan -> loan_officer_1_commission_type;
                                                                } else if($index == '2') {
                                                                    if($loan -> loan_officer_2_loan_amount_percent) {
                                                                        $loan_amount_percent = $loan -> loan_officer_2_loan_amount_percent;
                                                                    }
                                                                    $loan_officer_commission_type = $loan -> loan_officer_2_commission_type;
                                                                }
                                                                @endphp
                                                                <input type="hidden" name="loan_officer_{{ $index }}_loan_amount_percent" value="{{ $loan_amount_percent }}">
                                                            @endif

                                                            <input type="hidden" class="commission-paid-out" name="loan_officer_{{ $index }}_commission_amount" id="loan_officer_{{ $index }}_commission_amount">

                                                            <input type="hidden" name="loan_officer_{{ $index }}_commission_type" id="loan_officer_{{ $index }}_commission_type" value="{{ $loan_officer_commission_type ?? null }}">

                                                            <div class="my-4 pt-4 border-t">

                                                                <div class="flex justify-start">
                                                                    <div>
                                                                        Deductions
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="button primary sm no-text ml-3"
                                                                        @click="add_loan_officer_deduction(index)">
                                                                            <i class="fal fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <div class="loan-officer-{{ $index }}-deductions">

                                                                    @if($loan_officer_deductions)

                                                                    @forelse($loan_officer_deductions -> where('lo_index', $index) as $loan_officer_deduction)

                                                                        <div class="flex justify-between items-end loan-officer-{{ $index }}-deduction">

                                                                            <div class="grid grid-cols-1 sm:grid-cols-3">

                                                                                <div class="mr-2 mb-2">
                                                                                    <input
                                                                                    type="text"
                                                                                    class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                                                                                    name="loan_officer_deduction_amount[]"
                                                                                    data-label="Amount"
                                                                                    value="{{ $loan_officer_deduction -> amount }}">
                                                                                </div>

                                                                                <div class="col-span-2 mb-2">
                                                                                    <input
                                                                                    type="text"
                                                                                    class="form-element input {{ $input_size }} required"
                                                                                    name="loan_officer_deduction_description[]"
                                                                                    data-label="Description"
                                                                                    value="{{ $loan_officer_deduction -> description }}">
                                                                                </div>

                                                                                <input type="hidden" name="loan_officer_deduction_index[]" value="{{ $index }}">
                                                                            </div>

                                                                            <div class="flex items-end mb-3 ml-3">
                                                                                <button type="button" class="button danger md no-text"
                                                                                @click.prevent="$el.closest('.loan-officer-{{ $index }}-deduction').remove(); total_commission()">
                                                                                    <i class="fal fa-times"></i>
                                                                                </button>
                                                                            </div>

                                                                        </div>

                                                                    @empty

                                                                    @endforelse

                                                                    @endif

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            @endforeach


                                            <div class="p-4 border rounded-md"
                                            x-data="{ show_details: false }">

                                                <div class="flex justify-between items-center">

                                                    <div class="flex jusify-start items-center cursor-pointer w-full" @click="show_details = !show_details">

                                                        <div class="mr-6">
                                                            <a href="javascript:void(0)" class="block">
                                                                <i :class="{ 'fas fa-plus-circle fa-lg text-yellow-600': show_details === false, 'fas fa-times-circle fa-lg text-red-600': show_details === true }"></i>
                                                            </a>
                                                        </div>

                                                        <div class="flex items-center w-96">
                                                            <div class="flex-1 text-lg text-gray-800">
                                                                {{ $manager }}
                                                            </div>
                                                            <div class="flex-1 text-gray-400">Manager</div>
                                                        </div>

                                                    </div>

                                                    <div class="col-span-1 whitespace-nowrap">

                                                        <div class="text-right bg-primary-lightest text-primary-dark p-2 rounded-md">
                                                            <span class="manager-commission-amount-ele">$0.00</span>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div x-show="show_details" x-transition>

                                                    <div class="mt-6 ml-3">
                                                        {{ $manager_bonus_details }}
                                                    </div>

                                                    <div class="flex m-2 sm:m-3 mt-5 bg-primary-lightest text-primary-dark p-4 rounded-md">

                                                        <div>
                                                            {{ $manager_bonus }}%
                                                        </div>

                                                        <div class="ml-4">
                                                            of
                                                        </div>

                                                        <div class="ml-4">
                                                            <span class="net-commission-amount ml-3"></span>
                                                        </div>

                                                        <div class="ml-4">
                                                            =
                                                        </div>

                                                        <div class="ml-4 font-bold">
                                                            <span class="manager-commission-amount-ele"></span>
                                                        </div>

                                                        <input type="hidden" class="commission-paid-out" name="manager_bonus" id="manager_bonus">

                                                    </div>

                                                </div>

                                            </div>


                                        </div>

                                        <div class="col-span-1 ml-8 whitespace-nowrap place-self-end w-full">

                                            <div class="text-lg text-right bg-red-50 text-red-600 p-4 rounded-md">
                                                <div class="text-xs">Commissions Out</div>
                                                <span id="commissions_paid_amount">$0.00</span>
                                            </div>

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6">

                                    <div class="grid grid-cols-5">

                                        <div class="col-span-4 flex items-center font-bold text-xl">Company Commission</div>

                                        <div class="col-span-1 ml-7 whitespace-nowrap">

                                            <div class="text-right bg-green-50 text-green-600 p-4 rounded-md">
                                                <div class="text-xs">Company Commisison</div>
                                                <span id="company_commission_amount">$0.00</span>
                                            </div>
                                            <input type="hidden" name="company_commission" id="company_commission">

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6">


                                    <div class="p-8 flex justify-around">
                                        <button type="button" class="button primary xl" @click="save_commission($el)"><i class="fal fa-check mr-3"></i> Save Commission</button>
                                    </div>

                                    <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">


                                    <hr class="bg-gray-300 my-6">

                                    <div class="font-medium text-xl">Notes</div>

                                </div>

                                <div class="col-span-7 lg:col-span-2">

                                    <div class="sticky top-12">

                                        <div class="bg-primary-lightest ml-0 lg:ml-8 border-4 rounded-xl">

                                            <div class="bg-primary text-white text-lg rounded-t-lg p-2">Checks Out</div>

                                            <div class="px-2 pt-4">

                                                <div class="grid grid-cols-3 border-b border-white pb-2">
                                                    <div class="col-span-2 border-b">
                                                        {{ $loan_officer_1 -> fullname ?? null }}
                                                    </div>
                                                    <div>
                                                        <span class="loan-officer-1-commission-amount-ele"></span>
                                                    </div>

                                                    <div class="col-span-2 text-sm text-gray-500">
                                                        Loan Officer
                                                    </div>
                                                </div>

                                                @if($loan_officer_2)
                                                <div class="grid grid-cols-3 border-b border-white py-2">
                                                    <div class="col-span-2 border-b">
                                                        {{ $loan_officer_2 -> fullname }}
                                                    </div>
                                                    <div>
                                                        <span class="loan-officer-2-commission-amount-ele"></span>
                                                    </div>

                                                    <div class="col-span-2 text-sm text-gray-500">
                                                        Loan Officer
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="grid grid-cols-3 border-b border-white py-2">
                                                    <div class="col-span-2 border-b">
                                                        {{ $manager }}
                                                    </div>
                                                    <div>
                                                        <span class="manager-commission-amount-ele"></span>
                                                    </div>

                                                    <div class="col-span-2 text-sm text-gray-500">
                                                        Manager
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="p-2 pt-0 deductions-out-div"></div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                        </form>

                    </div>


                </div>


                <div x-show="active_tab === '3'" class="pt-4 sm:pt-12">



                </div>

            </div>

        </div>


        <template id="check_in_template">

            <div class="flex justify-start items-end mt-2 check-in">

                <div class="w-36">
                    <input
                    type="text"
                    class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                    name="check_in_amount[]"
                    data-label="Check Amount"
                    value=""
                    @keyup="set_checks_in_amount();">
                </div>

                <div class="mx-2 mb-1">
                    <button type="button" class="button danger md no-text delete-check-in-button"
                    @click="$el.closest('.check-in').remove(); total_commission(); run_show_delete_check_in();"
                    x-show="show_delete_check_in">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

            </div>

        </template>

        <template id="deduction_template">

            <div class="flex justify-between items-end deduction">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-9"
                x-data="{ show_other: false }">

                    <div class="col-span-2 mr-2 mb-2">
                        <input
                        type="text"
                        class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                        name="amount[]"
                        data-label="Amount">
                    </div>

                    <div class="col-span-1 sm:col-span-2 md:col-span-3 mr-2 mb-2">
                        <input
                        type="text"
                        class="form-element input {{ $input_size }} commission-input required"
                        name="description[]"
                        data-label="Description">
                    </div>

                    <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                        <select
                        class="form-element select {{ $input_size }} required"
                        name="paid_to[]"
                        data-label="Paid To"
                        @change="show_other = false; if($el.value == 'Other') { show_other = true }; total_commission();">
                            <option value=""></option>
                            <option value="Company">Company</option>
                            <option value="Loan Officer 1">{{ $loan_officer_1 -> fullname ?? 'Loan Officer 1' }}</option>
                            @if($loan_officer_2)
                            <option value="Loan Officer 2">{{ $loan_officer_2 -> fullname ?? 'Loan Officer 2' }}</option>
                            @endif
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                        <div x-show="show_other">
                            <input
                            type="text"
                            class="form-element input {{ $input_size }} commission-input required"
                            name="paid_to_other[]"
                            data-label="Paid To Name">
                        </div>
                    </div>

                </div>

                <div class="flex items-end mb-2 ml-3">
                    <button type="button" class="button danger md no-text"
                    @click.prevent="$el.closest('.deduction').remove(); total_commission()">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

            </div>

        </template>


        <template id="loan_officer_deduction_template">

            <div class="flex justify-between items-end loan-officer-%%index%%-deduction">

                <div class="grid grid-cols-1 sm:grid-cols-3">

                    <div class="mr-2 mb-2">
                        <input
                        type="text"
                        class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                        name="loan_officer_deduction_amount[]"
                        data-label="Amount">
                    </div>

                    <div class="col-span-2 mb-2">
                        <input
                        type="text"
                        class="form-element input {{ $input_size }} required"
                        name="loan_officer_deduction_description[]"
                        data-label="Description">
                    </div>

                    <input type="hidden" name="loan_officer_deduction_index[]" value="%%index%%">
                </div>

                <div class="flex items-end mb-3 ml-3">
                    <button type="button" class="button danger md no-text"
                    @click.prevent="$el.closest('.loan-officer-%%index%%-deduction').remove(); total_commission()">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

            </div>

        </template>

    </div>


</x-app-layout>
