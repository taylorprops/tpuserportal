@php
$title = $loan ? $loan -> street : 'Add Loan';

$breadcrumbs = [
    ['Heritage Financial', ''],
    ['Loans', '/heritage_financial/loans'],
    [$title, ''],
];
$input_size = 'md';

$active_tab = '1';
if(isset($_GET['tab']) && $_GET['tab'] == 'commission') {
    $active_tab = '3';
}

$disabled = '';

@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-48 pt-2"
    x-data="loan(
        '{{ $loan -> uuid ?? null }}',
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
                    @if(auth() -> user() -> level != 'loan_officer')
                    <option value="2">Timeline</option>
                    @endif
                    <option value="3">Commission</option>
                    <option value="4">Documents</option>
                    @if(auth() -> user() -> level != 'loan_officer')
                    <option value="5">Audit</option>
                    @endif
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

                            @if(auth() -> user() -> level != 'loan_officer')

                                <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                                :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2', 'border-primary text-primary-dark': active_tab === '2' }"
                                @click="active_tab = '2'">
                                    <i class="fad fa-calendar mr-3"
                                    :class="{ 'text-primary': active_tab === '2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2' }"></i>
                                    <span>Timeline</span>
                                </a>
                            @endif

                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3', 'border-primary text-primary-dark': active_tab === '3' }"
                            @click="active_tab = '3'">
                                <i class="fad fa-calculator mr-3"
                                :class="{ 'text-primary': active_tab === '3', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3' }"></i>
                                <span>Commission</span>
                            </a>

                            <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4', 'border-primary text-primary-dark': active_tab === '4' }"
                            @click="active_tab = '4'">
                                <i class="fad fa-copy mr-3"
                                :class="{ 'text-primary': active_tab === '4', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '4' }"></i>
                                <span>Documents</span>
                            </a>

                            @if(auth() -> user() -> level != 'loan_officer')

                                <a href="javascript:void(0)" class="group inline-flex items-center py-4 px-3 border-b-2 font-medium"
                                :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '5', 'border-primary text-primary-dark': active_tab === '5' }"
                                @click="active_tab = '5'">
                                    <i class="fad fa-copy mr-3"
                                    :class="{ 'text-primary': active_tab === '5', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '5' }"></i>
                                    <span>Audit</span>
                                </a>

                            @endif

                        @endif
                    </nav>

                </div>

            </div>

            <div>

                <div x-show="active_tab === '1'" x-transition class="pt-4 sm:pt-12 max-w-1000-px">

                    <form id="details_form">

                        <div class="font-medium text-gray-700 border-b mb-2"><i class="fad fa-users mr-2"></i> People</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select {{ $input_size }} required"
                                id="loan_officer_1_id"
                                name="loan_officer_1_id"
                                data-label="Loan Officer">
                                @if(auth() -> user() -> level == 'loan_officer')
                                    <option value="{{ $loan -> loan_officer_1_id }}">{{ $loan -> loan_officer_1 -> fullname }}</option>
                                @else
                                    <option value=""></option>
                                    @foreach($loan_officers -> where('emp_position', 'loan_officer') as $lo)
                                    <option value="{{ $lo -> id }}" @if($loan && $loan -> loan_officer_1_id == $lo -> id) selected @endif>{{ $lo -> last_name }}, {{ $lo -> first_name }}</option>
                                    @endforeach
                                @endif
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
                                id="agent_name_seller"
                                name="agent_name_seller"
                                data-label="Seller Agent Name"
                                value="{{ $loan -> agent_name_seller ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input {{ $input_size }}"
                                id="agent_company_seller"
                                name="agent_company_seller"
                                data-label="Seller Agent Company"
                                value="{{ $loan -> agent_company_seller ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input {{ $input_size }}"
                                id="agent_name_buyer"
                                name="agent_name_buyer"
                                data-label="Buyer Agent Name"
                                value="{{ $loan -> agent_name_buyer ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input {{ $input_size }}"
                                id="agent_company_buyer"
                                name="agent_company_buyer"
                                data-label="Buyer Agent Company"
                                value="{{ $loan -> agent_company_buyer ?? null }}">
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
                                class="form-element select {{ $input_size }} @if($loan && $loan -> loan_status == 'Closed') required @endif"
                                name="title_company_select"
                                id="title_company_select"
                                x-ref="title_company_select"
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
                                class="form-element input {{ $input_size }} @if($loan && $loan -> loan_status == 'Closed') required @endif"
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

                            {{-- Points Charged --}}
                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input {{ $input_size }} numbers-only required"
                                id="points_charged"
                                name="points_charged"
                                data-label="Points Charged"
                                value="{{ $loan -> points_charged ?? '0.00' }}">
                            </div>

                            {{-- Loan Source --}}
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

                            {{-- Mortgage Type --}}
                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select {{ $input_size }} required"
                                id="mortgage_type"
                                name="mortgage_type"
                                data-label="Mortgage Type">
                                    <option value=""></option>
                                    <option value="first" @if($loan && $loan -> mortgage_type == 'first') selected @endif>First</option>
                                    <option value="second" @if($loan && $loan -> mortgage_type == 'second') selected @endif>Second</option>
                                </select>
                            </div>

                            {{-- Reverse Mortgage --}}
                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select {{ $input_size }} required"
                                id="reverse"
                                name="reverse"
                                data-label="Reverse Mortgage">
                                    <option value=""></option>
                                    <option value="yes" @if($loan && $loan -> reverse == 'yes') selected @endif>Yes</option>
                                    <option value="no" @if($loan && $loan -> reverse == 'no') selected @endif>No</option>
                                </select>
                            </div>

                        </div>

                        @if($loan)

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                {{-- Loan Status --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    @php
                                    $loan_status_detailed_open = $loan && $loan -> loan_status == 'Open' ? ' / '.$loan -> loan_status_detailed : null;
                                    $loan_status_detailed_closed = $loan && $loan -> loan_status == 'Closed' ? ' / '.$loan -> loan_status_detailed : null;
                                    $loan_status_detailed_cancelled = $loan && $loan -> loan_status == 'Cancelled' ? ' / '.$loan -> loan_status_detailed : null;

                                    @endphp
                                    <select
                                    class="form-element select {{ $input_size }}"
                                    id="loan_status"
                                    name="loan_status"
                                    data-label="Loan Status"
                                    {{ $disabled }}
                                    @change="require_title($el.value);
                                    require_close_date($el.value);
                                    let label_text = $el.value == 'Cancelled' ? 'Cancel Date' : 'Settlement Date';
                                    $refs.settlement_date.previousElementSibling.innerText = label_text;">
                                        <option value=""></option>
                                        <option value="Open" @if($loan && $loan -> loan_status == 'Open') selected @endif>Open {{ $loan_status_detailed_open }}</option>
                                        <option value="Closed" @if($loan && $loan -> loan_status == 'Closed') selected @endif>Closed {{ $loan_status_detailed_closed }}</option>
                                        <option value="Cancelled" @if($loan && $loan -> loan_status == 'Cancelled') selected @endif>Cancelled {{ $loan_status_detailed_cancelled }}</option>
                                    </select>
                                </div>

                                {{-- Settlement Date --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="date"
                                    class="form-element input {{ $input_size }}"
                                    id="settlement_date"
                                    name="settlement_date"
                                    x-ref="settlement_date"
                                    data-label=" @if($loan && $loan -> loan_status == 'Cancelled') Cancelled Date @else Settlement Date @endif"
                                    {{ $disabled }}
                                    value="{{ $loan -> settlement_date ?? null }}">
                                </div>

                                {{-- Loan Amount --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }} numbers-only money-decimal"
                                    id="loan_amount"
                                    name="loan_amount"
                                    data-label="Loan Amount"
                                    {{ $disabled }}
                                    value="{{ $loan -> loan_amount ?? null }}">
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                {{-- Lender --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }}"
                                    id="lender_uuid"
                                    name="lender_uuid"
                                    data-label="Lender"
                                    {{ $disabled }}>
                                        <option value=""></option>
                                        @foreach($lenders as $lender)
                                            <option value="{{ $lender -> uuid }}" @if($loan && $loan -> lender_uuid == $lender -> uuid) selected @endif>{{ $lender -> company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Loan Type --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }}"
                                    id="loan_type"
                                    name="loan_type"
                                    data-label="Loan Type"
                                    {{ $disabled }}>
                                        <option value=""></option>
                                        <option value="Conventional" @if($loan && $loan -> loan_type == 'Conventional') selected @endif>Conventional</option>
                                        <option value="FHA" @if($loan && $loan -> loan_type == 'FHA') selected @endif>FHA</option>
                                        <option value="VA" @if($loan && $loan -> loan_type == 'VA') selected @endif>VA</option>
                                        <option value="USDA" @if($loan && $loan -> loan_type == 'USDA') selected @endif>USDA</option>
                                        <option value="HELOC" @if($loan && $loan -> loan_type == 'HELOC') selected @endif>HELOC</option>
                                        <option value="HEL" @if($loan && $loan -> loan_type == 'HEL') selected @endif>HEL</option>
                                        <option value="Other" @if($loan && $loan -> loan_type == 'Other') selected @endif>Other</option>
                                    </select>
                                </div>

                                {{-- Loan Purpose --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <select
                                    class="form-element select {{ $input_size }}"
                                    id="loan_purpose"
                                    name="loan_purpose"
                                    data-label="Loan Purpose"
                                    {{ $disabled }}>
                                        <option value=""></option>
                                        <option value="Purchase" @if($loan && $loan -> loan_purpose == 'Purchase') selected @endif>Purchase</option>
                                        <option value="Cash Out Refinance" @if($loan && $loan -> loan_purpose == 'Cash Out Refinance') selected @endif>Cash Out Refinance</option>
                                        <option value="No Cash Out Refinance" @if($loan && $loan -> loan_purpose == 'No Cash Out Refinance') selected @endif>No Cash Out Refinance</option>
                                        <option value="Construction" @if($loan && $loan -> loan_purpose == 'Construction') selected @endif>Construction</option>
                                        <option value="Construction Permanent" @if($loan && $loan -> loan_purpose == 'Construction Permanent') selected @endif>Construction Permanent</option>
                                        <option value="Other" @if($loan && $loan -> loan_purpose == 'Other') selected @endif>Other</option>
                                    </select>
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                                {{-- Loan Number --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="loan_number"
                                    name="loan_number"
                                    data-label="Loan Number"
                                    {{ $disabled }}
                                    value="{{ $loan -> loan_number ?? null }}">
                                </div>

                                {{-- Lending Pad Loan Number --}}
                                <div class="col-span-1 m-2 sm:m-3">
                                    <input
                                    type="text"
                                    class="form-element input {{ $input_size }}"
                                    id="lending_pad_loan_number"
                                    name="lending_pad_loan_number"
                                    data-label="Lending Pad Loan Number"
                                    {{ $disabled }}
                                    value="{{ $loan -> lending_pad_loan_number ?? null }}">
                                </div>

                            </div>

                        @endif

                        <hr class="bg-gray-300 my-6">

                        <div class="p-8 flex justify-around">
                            <button type="button" class="button primary xl" @click="save_details($el)">Save Details <i class="fal fa-check ml-2"></i></button>
                        </div>

                        <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                    </form>

                </div>

                @if(auth() -> user() -> level != 'loan_officer')

                    <div x-show="active_tab === '2'" x-transition class="pt-4 sm:pt-12 max-w-600-px">

                        <form id="time_line_form">

                            <div class="mt-12 border-2 rounded-lg p-4 divide-y">

                                @php
                                $fields = [
                                    ['select', 'locked', 'Locked', $disabled],
                                    ['date', 'lock_date', 'Lock Date', $disabled],
                                    ['date', 'lock_expiration', 'Lock Expiration', $disabled],
                                    ['date', 'time_line_package_to_borrower', 'Package Sent To Borrower', ''],
                                    ['date', 'time_line_sent_to_processing', 'Sent To Processing', $disabled],
                                    ['date', 'time_line_clear_to_close', 'Clear To Close', $disabled],
                                    ['date', 'time_line_scheduled_settlement', 'Scheduled Settlement Date', ''],
                                    ['date', 'time_line_closed', 'Closed', $disabled],
                                    ['date', 'time_line_funded', 'Funded', $disabled]
                                ];
                                $processing_fields = [
                                    ['select', 'time_line_conditions_received_status', 'Conditions Received Status', $disabled],
                                    ['date', 'time_line_conditions_received', 'Conditions Received', ''],
                                    ['date', 'time_line_title_ordered', 'Title Ordered', ''],
                                    ['date', 'time_line_title_received', 'Title Received', ''],
                                    ['date', 'time_line_submitted_to_uw', 'Submitted To UW', ''],
                                    ['date', 'time_line_appraisal_ordered', 'Appraisal Ordered', ''],
                                    ['date', 'time_line_appraisal_received', 'Appraisal Received', $disabled],
                                    ['date', 'time_line_voe_ordered', 'VOE Ordered', ''],
                                    ['date', 'time_line_voe_received', 'VOE Received', ''],
                                    ['date', 'time_line_conditions_submitted', 'Conditions Submitted', $disabled],
                                ];
                                @endphp

                                @foreach($fields as $field)

                                    @php $db_field = $field[1]; @endphp

                                    <div class="grid grid-cols-5 py-2">

                                        <div class="col-span-3 flex justify-end items-center mr-4">
                                            {{ $field[2] }}
                                        </div>

                                        <div class="col-span-2">

                                            @if($field[0] != 'select')
                                                <input type="{{ $field[0] }}" class="form-element input md" name="{{ $db_field }}" value="{{ $loan -> $db_field ?? null }}" {{ $field[3] }}>
                                            @else
                                                <select
                                                class="form-element select md"
                                                name="{{ $db_field }}"
                                                {{ $field[3] }}
                                                data-label="">
                                                    <option value="None" @if($loan && $loan -> $db_field == 'None') selected @endif></option>
                                                    <option value="Registered" @if($loan && $loan -> $db_field == 'Registered') selected @endif>Registered</option>
                                                    <option value="Locked" @if($loan && $loan -> $db_field == 'Locked') selected @endif>Locked</option>
                                                    <option value="Expired" @if($loan && $loan -> $db_field == 'Expired') selected @endif>Expired</option>
                                                    <option value="Cancelled" @if($loan && $loan -> $db_field == 'Cancelled') selected @endif>Cancelled</option>
                                                    <option value="Withdrawn" @if($loan && $loan -> $db_field == 'Withdrawn') selected @endif>Withdrawn</option>
                                                    <option value="Float" @if($loan && $loan -> $db_field == 'Float') selected @endif>Float</option>
                                                    <option value="LockPending" @if($loan && $loan -> $db_field == 'LockPending') selected @endif>LockPending</option>
                                                    <option value="RegisteredManual" @if($loan && $loan -> $db_field == 'RegisteredManual') selected @endif>RegisteredManual</option>
                                                    <option value="LockPendingManual" @if($loan && $loan -> $db_field == 'LockPendingManual') selected @endif>LockPendingManual</option>
                                                    <option value="LockedManual" @if($loan && $loan -> $db_field == 'LockedManual') selected @endif>LockedManual</option>
                                                </select>
                                            @endif

                                        </div>

                                        @if($db_field == 'time_line_sent_to_processing')

                                            <div class="col-span-5 mt-4 border p-4 rounded-md bg-gray-50 divide-y">

                                                <div class="text-gray-400 text-lg font-semibold text-center mb-3">Processing Tasks</div>

                                                @foreach($processing_fields as $processing_field)

                                                    @php $db_processing_field = $processing_field[1]; @endphp

                                                    <div class="grid grid-cols-5 py-2">

                                                        <div class="col-span-3 flex justify-end items-center mr-4">
                                                            {{ $processing_field[2] }}
                                                        </div>

                                                        <div class="col-span-2">

                                                            @if($processing_field[0] != 'select')
                                                                <input type="{{ $processing_field[0] }}" class="form-element input md" name="{{ $db_processing_field }}" value="{{ $loan -> $db_processing_field ?? null }}" {{ $processing_field[3] }}>
                                                            @else
                                                                <select
                                                                class="form-element select md"
                                                                name="{{ $db_processing_field }}"
                                                                {{ $processing_field[3] }}
                                                                data-label="">
                                                                    <option value=""></option>
                                                                    <option value="approved" @if($loan && $loan -> $db_processing_field == 'approved') selected @endif>Approved</option>
                                                                    <option value="suspended" @if($loan && $loan -> $db_processing_field == 'suspended') selected @endif>Suspended</option>
                                                                </select>
                                                            @endif

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    </div>

                                @endforeach

                                <div class="mt-4 flex justify-around p-6">
                                    <button type="button" class="button primary lg" @click="save_time_line($el)">Save Timeline <i class="fal fa-check ml-2"></i></button>
                                </div>

                            </div>

                            <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                        </form>

                    </div>

                @endif


                <div x-show="active_tab === '3'" x-transition class="pt-4 sm:pt-12">

                    {{-- if Loan Officer --}}
                    <div class="mt-4 @if($loan && $loan -> loan_status == 'Closed') max-w-xl @else max-w-4xl @endif @if(auth() -> user() -> level != 'loan_officer') hidden @endif">

                        <div class="@if($loan && $loan -> loan_status != 'Closed') hidden @endif">

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

                        <div class="@if($loan && $loan -> loan_status == 'Closed') hidden @endif">

                            @if(auth() -> user() -> level == 'loan_officer')

                                <form id="commission_form_lo">

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
                                                                <div x-show="show_other" x-transition>
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

                                    <div class="mt-8 flex justify-start">
                                        <button type="button" class="button primary lg" @click="save_commission($el, 'lo')">Save Deductions <i class="fal fa-check ml-2"></i></button>
                                    </div>

                                    <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">
                                    <input type="hidden" name="form_type" value="lo">

                                </form>

                            @endif

                        </div>

                    </div>

                    {{-- if Processor or Manager --}}
                    <div @if(auth() -> user() -> level == 'loan_officer') class="hidden" @endif>

                        <div class="grid grid-cols-7 max-w-1400-px gap-16">

                            <div class="col-span-7 lg:col-span-5">

                                <form id="commission_form">


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
                                                        x-show="show_delete_check_in" x-transition>
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
                                                        x-show="show_delete_check_in" x-transition>
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

                                    @if(auth() -> user() -> level != 'loan_officer')

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

                                                    @if(count($deductions) == 0)

                                                        <div class="flex justify-between items-end deduction">

                                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-9"
                                                            x-data="{ show_other: false }">

                                                                <div class="col-span-2 mr-2 mb-2">
                                                                    <input
                                                                    type="text"
                                                                    class="form-element input {{ $input_size }} numbers-only money-decimal commission-input required"
                                                                    name="amount[]"
                                                                    data-label="Amount"
                                                                    value="$500.00">
                                                                </div>

                                                                <div class="col-span-1 sm:col-span-2 md:col-span-3 mr-2 mb-2">
                                                                    <input
                                                                    type="text"
                                                                    class="form-element input {{ $input_size }} commission-input required"
                                                                    name="description[]"
                                                                    data-label="Description"
                                                                    value="Processing Fee">
                                                                </div>

                                                                <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                                                                    <select
                                                                    class="form-element select {{ $input_size }} required"
                                                                    name="paid_to[]"
                                                                    data-label="Paid To"
                                                                    @change="show_other = false; if($el.value == 'Other') { show_other = true }; total_commission();">
                                                                        <option value=""></option>
                                                                        <option value="Company" selected>Company</option>
                                                                        <option value="Loan Officer 1">{{ $loan_officer_1 -> fullname ?? 'Loan Officer 1' }}</option>
                                                                        @if($loan_officer_2)
                                                                        <option value="Loan Officer 2">{{ $loan_officer_2 -> fullname ?? 'Loan Officer 2' }}</option>
                                                                        @endif
                                                                        <option value="Other">Other</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                                                                    <div x-show="show_other" x-transition>
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

                                                    @endif

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
                                                                    <div x-show="show_other" x-transition>
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

                                                        {{-- <div class="flex items-center text-gray-400 h-20" x-ref="no_deductions"><i class="fad fa-minus-circle mr-2"></i> No Deductions Added</div> --}}

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

                                    @endif

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

                                                            <div x-show="active_commission_tab === '1'" x-transition>

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

                                                            <div x-show="active_commission_tab === '2'" x-transition>

                                                                <div class="flex justify-start items-center mb-3 bg-primary-lightest text-primary-dark p-2 rounded-md"
                                                                x-show="show_alert" x-transition>
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


                                            <div class="p-4 border rounded-md @if(auth() -> user() -> level != 'manager' && auth() -> user() -> level != 'super_admin') hidden @endif"
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

                                            <div class="text-lg text-right bg-red-50 text-red-600 p-4 rounded-md
                                            @if(auth() -> user() -> level != 'manager' && auth() -> user() -> level != 'super_admin') hidden @endif">
                                                <div class="text-xs">Commissions Out</div>
                                                <span id="commissions_paid_amount">$0.00</span>
                                            </div>

                                        </div>

                                    </div>

                                    <hr class="bg-gray-300 my-6 @if(auth() -> user() -> level != 'manager' && auth() -> user() -> level != 'super_admin') hidden @endif">

                                    <div class="grid grid-cols-5 @if(auth() -> user() -> level != 'manager' && auth() -> user() -> level != 'super_admin') hidden @endif">

                                        <div class="col-span-4 flex items-center font-bold text-xl">Company Commission</div>

                                        <div class="col-span-1 ml-7 whitespace-nowrap">

                                            <div class="text-right bg-green-50 text-green-600 p-4 rounded-md">
                                                <div class="text-xs">Company Commisison</div>
                                                <span class="company-commission-amount">$0.00</span>
                                            </div>
                                            <input type="hidden" name="company_commission" id="company_commission">

                                        </div>

                                    </div>


                                    @if(auth() -> user() -> level != 'loan_officer')

                                        <hr class="bg-gray-300 my-6">

                                        <div class="p-8 flex justify-around">
                                            <button type="button" class="button primary xl" @click="save_commission($el, '')">Save Commission <i class="fal fa-check ml-2"></i></button>
                                        </div>

                                        <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">


                                    @endif

                                </form>




                            </div>

                            <div class="col-span-7 lg:col-span-2">

                                <div class="sticky top-12 ml-0 lg:ml-8">

                                    <div>

                                        <div class="bg-primary-lightest border-4 rounded-xl">

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

                                                @if(auth() -> user() -> level == 'manager' || auth() -> user() -> level == 'super_admin')
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
                                                @endif

                                            </div>

                                            <div class="p-2 pt-0 deductions-out-div"></div>

                                        </div>

                                    </div>

                                    <div class="flex justify-around my-3">
                                        @if(auth() -> user() -> level == 'manager' || auth() -> user() -> level == 'super_admin')
                                        <button type="button" class="button primary md" @click="print_checks_out()"><i class="fad fa-print mr-2"></i> Print</button>
                                        @endif
                                    </div>

                                    <div class="printable-checks-out hidden">

                                        <span style="font-size: 20px; font-weight: bold; font-family:Arial, Helvetica, sans-serif">
                                            @if($loan)
                                            {{ $loan -> street.' '.$loan -> city.', '.$loan -> state.' '.$loan -> zip}}
                                            @endif
                                        </span>
                                        <br><br>
                                        <table style="font-family:Arial, Helvetica, sans-serif">
                                            <tr>
                                                <th align="left" style="padding-bottom: 10px; font-size: 18px">Checks In</th>
                                            </tr>
                                            <tr>
                                                <td><span class="checks-in-amount"></span></td>
                                            </tr>
                                        </table>

                                        <hr>

                                        <table style="font-family:Arial, Helvetica, sans-serif; margin-top: 10px">
                                            <tr>
                                                <th colspan="3" align="left" style="padding-bottom: 20px; font-size: 18px">Checks Out</th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" align="left">Commission/Bonuses</th>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 10px">{{ $loan_officer_1 -> fullname ?? null }}</td>
                                                <td style="padding-right: 10px">Loan Officer</td>
                                                <td><span class="loan-officer-1-commission-amount-ele"></span></td>
                                            </tr>

                                            @if($loan_officer_2)
                                                <tr>
                                                    <td style="padding-right: 10px">{{ $loan_officer_2 -> fullname }}</td>
                                                    <td style="padding-right: 10px">Loan Officer</td>
                                                    <td><span class="loan-officer-2-commission-amount-ele"></span></td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td style="padding-right: 10px">{{ $manager }}</td>
                                                <td style="padding-right: 10px">Manager</td>
                                                <td><span class="manager-commission-amount-ele"></span></td>
                                            </tr>

                                        </table>

                                        <div class="deductions-out-div-print"></div>

                                        <hr>

                                        <table style="font-family:Arial, Helvetica, sans-serif; margin-top: 20px">
                                            <tr>
                                                <td>Processing Fee:</td>
                                                <td>
                                                    <span class="processing-fee"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Net Commission:</td>
                                                <td>
                                                    <span class="company-commission-amount"></span>
                                                </td>
                                            </tr>
                                        </table>

                                        <hr>

                                        <table style="font-family:Arial, Helvetica, sans-serif; margin-top: 20px">
                                            <tr>
                                                <th align="left" style="padding-bottom: 10px; font-size: 18px">Notes</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div x-ref="notes_div_print"></div>
                                                </td>
                                            </tr>
                                        </table>

                                    </div>


                                    <div class="max-w-600-px mt-20">

                                        <div class="flex justify-between">
                                            <div class="font-medium text-xl">Notes</div>
                                            <div>
                                                <button type="button" class="button primary md"
                                                @click="show_add_notes = ! show_add_notes"
                                                x-show="show_add_notes === false">
                                                    <i class="fal fa-plus mr-2"></i> Add Note
                                                </button>
                                                <button type="button" class="button danger md no-text"
                                                @click="show_add_notes = ! show_add_notes"
                                                x-show="show_add_notes === true">
                                                    <i class="fal fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="border rounded-md p-4 my-3"
                                        x-show="show_add_notes" x-transition>
                                            <form id="add_notes_form">
                                                <div>
                                                    <textarea class="form-element textarea md" name="notes" id="notes"
                                                    x-ref="notes"></textarea>
                                                </div>
                                                <div class="flex justify-around mt-3">
                                                    <button type="button" class="button primary md"
                                                    @click.prevent="add_notes($el)">
                                                        Save Note <i class="fal fa-check ml-2"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>

                                    <div class="border-t-2 mt-4 max-w-600-px"
                                    x-ref="notes_div"></div>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>


                <div x-show="active_tab === '4'" x-transition class="pt-4 sm:pt-12">

                    <div class="">

                        <div class="max-w-sm">
                            <div class="text-gray-800 text-xl mb-8">Add Documents</div>
                            <input
                            type="file"
                            class="form-element input md"
                            id="loan_docs"
                            name="loan_docs"
                            multiple>
                        </div>

                    </div>

                    <div class="mt-12 max-w-4xl">

                        <div class="text-gray-800 text-xl mb-8">Uploaded Documents</div>

                        <div class="border-b mb-4 pb-2 flex justify-start items-center">
                            <div>
                                <input type="checkbox" class="form-element checkbox md primary"
                                data-label="Select All"
                                id="check_all"
                                @click="check_all()">
                            </div>
                            <div class="flex justify-start items-center ml-6" x-ref="bulk_options">
                                <button type="button" class="button primary sm" disabled="true" @click="delete_docs()">Delete Selected</button>
                            </div>
                        </div>

                        <div class="docs-div text-sm"></div>

                        <div class="mt-6 pt-8" x-show="show_deleted_docs_div" x-transition>

                            <button type="button" class="button primary sm" @click="show_deleted = !show_deleted">Show Deleted Documents</button>

                            <div class="mt-12" x-show="show_deleted" x-transition>

                                <div class="border-b mb-4 pb-2 flex justify-start items-center">
                                    <div>
                                        <input type="checkbox" class="form-element checkbox md primary"
                                        data-label="Select All"
                                        id="check_all_deleted"
                                        @click="check_all(true)">
                                    </div>
                                    <div class="flex justify-start items-center ml-6" x-ref="bulk_options_deleted">
                                        <button type="button" class="button primary sm" disabled="true" @click="restore_docs()">Restore Selected</button>
                                    </div>
                                </div>

                                <div class="deleted-docs-div text-sm"></div>

                            </div>

                        </div>

                    </div>

                </div>

                @if(auth() -> user() -> level != 'loan_officer')

                    <div x-show="active_tab === '5'" x-transition class="pt-4 sm:pt-12 max-w-1000-px">

                        <div x-ref="changes_div"></div>

                    </div>

                @endif

            </div>

        </div>


        <template id="doc_template">

            <div class="flex justify-start items-center border-b mb-4 pb-2">

                <div class="mr-4">
                    <input type="checkbox" class="form-element checkbox md primary %%input_class%%" value="%%doc_id%%"
                    @click="show_bulk_options()">
                </div>

                <div class="flex flex-grow justify-between items-center">
                    <div class="mr-4 t">
                        <a href="%%url%%" target="_blank">%%file_name%%</a>
                    </div>
                    <div class="flex justify-end items-center">

                        <div class="mx-4 text-xs text-right whitespace-nowrap">%%file_size%%<br>%%created%%</div>

                        <div>
                            <button
                            type="button"
                            class="button danger md delete-button"
                            x-on:click="delete_docs([%%doc_id%%])">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                        <div>
                            <button
                            type="button"
                            class="button primary md restore-button"
                            x-on:click="restore_docs([%%doc_id%%])">
                                Restore <i class="fal fa-undo ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </template>

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
                        <div x-show="show_other" x-transition>
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
