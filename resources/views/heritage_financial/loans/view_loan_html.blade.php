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
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-48 pt-2"
    x-data="loan('{{ $loan_officer_1_commission_type }}', '{{ $loan_officer_2_commission_type ?? null }}', '{{ $loan_officer_2_commission_sub_type }}', '{{ $loan -> loan_amount ?? null }}', '{{ $loan_officer_1 -> loan_amount_percent ?? null }}', '{{ $loan_officer_2 -> loan_amount_percent ?? null }}')">

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

                    <form id="details_form">

                        <div class="font-medium text-gray-700 border-b mb-2"><i class="fad fa-users mr-2"></i> People</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select md required"
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
                                class="form-element select md"
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
                                class="form-element select md required"
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
                                class="form-element input md"
                                id="agent_name"
                                name="agent_name"
                                data-label="Agent Name"
                                value="{{ $loan -> agent_name ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md"
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
                                class="form-element input md required"
                                id="borrower_first"
                                name="borrower_first"
                                data-label="Borrower First"
                                value="{{ $loan -> borrower_first ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md required"
                                id="borrower_last"
                                name="borrower_last"
                                data-label="Borrower Last"
                                value="{{ $loan -> borrower_last ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md"
                                id="co_borrower_first"
                                name="co_borrower_first"
                                data-label="Co-Borrower First"
                                value="{{ $loan -> co_borrower_first ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md"
                                id="co_borrower_last"
                                name="co_borrower_last"
                                data-label="Co-Borrower Last"
                                value="{{ $loan -> co_borrower_last ?? null }}">
                            </div>

                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select md required"
                                name="title_company_select"
                                id="title_company_select"
                                data-label="Title Company"
                                @change="if($el.value != 'other') { $refs.title_company.value = $el.value } else { $refs.title_company.value = '' }">
                                    <option value=""></option>
                                    <option value="Heritage Title" @if($loan && $loan -> title_company == "Heritage Title") selected @endif>Heritage Title</option>
                                    <option value="Title Nation" @if($loan && $loan -> title_company == "Title Nation") selcted @endif>Title Nation</option>
                                    <option value="other" @if($loan && $loan -> title_company != 'Heritage Title' && $loan -> title_company != 'Title Nation') selected @endif>Other</option>
                                </select>
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md required"
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
                                class="form-element input md required"
                                id="street"
                                name="street"
                                data-label="Street"
                                value="{{ $loan -> street ?? null }}">
                            </div>

                            <div class="m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md required"
                                id="zip"
                                name="zip"
                                data-label="Zip"
                                value="{{ $loan -> zip ?? null }}"
                                x-on:keyup="get_location_details('#details_form', '', '#zip', '#city', '#state');">
                            </div>

                            <div class="m-2 sm:m-3 col-span-1 lg:col-span-2">
                                <input
                                type="text"
                                class="form-element input md required"
                                id="city"
                                name="city"
                                data-label="City"
                                value="{{ $loan -> city ?? null }}">
                            </div>

                            <div class="m-2 sm:m-3 col-span-1">
                                <select
                                class="form-element select md required"
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
                                <input
                                type="date"
                                class="form-element input md required"
                                id="settlement_date"
                                name="settlement_date"
                                data-label="Settlement Date"
                                value="{{ $loan -> settlement_date ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md numbers-only money-decimal required"
                                id="loan_amount"
                                name="loan_amount"
                                data-label="Loan Amount"
                                value="{{ $loan -> loan_amount ?? null }}">
                            </div>

                            <div class="col-span-1 m-2 sm:m-3">
                                <input
                                type="text"
                                class="form-element input md required"
                                id="loan_number"
                                name="loan_number"
                                data-label="Loan Number"
                                value="{{ $loan -> loan_number ?? null }}">
                            </div>

                        </div>

                        <hr class="bg-gray-300 my-6">

                        <div class="p-8 flex justify-around">
                            <button type="button" class="button primary xl" @click="save_details($el)"><i class="fal fa-check mr-3"></i> Save Details</button>
                        </div>

                        <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                    </form>

                </div>


                <div x-show="active_tab === '2'" class="pt-4 sm:pt-12">

                    <form id="commission_form">


                        <div class="grid grid-cols-4">

                            <div class="col-span-4 lg:col-span-3">


                                <div class="flex justify-start items-center">

                                    <div class="font-medium text-xl">Checks In</div>

                                    <button type="button" class="button primary sm no-text ml-3"
                                    @click="add_check_in()">
                                        <i class="fal fa-plus"></i>
                                    </button>

                                </div>

                                <div class="grid grid-cols-5 mt-4">

                                    <div class="col-span-4 checks-in grid grid-cols-3">

                                        @forelse($checks_in as $check_in)

                                            <div class="flex justify-start items-end mt-2 check-in">

                                                <div class="w-36">
                                                    <input
                                                    type="text"
                                                    class="form-element input md numbers-only money-decimal commission-input required"
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
                                                    class="form-element input md numbers-only money-decimal commission-input required"
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

                                    <div class="col-span-1 ml-8 whitespace-nowrap place-self-end">

                                        <div class="text-lg text-right bg-green-50 text-green-800 p-4 rounded-md">
                                            <span id="checks_in_amount_ele">$0.00</span>
                                        </div>

                                    </div>

                                </div>

                                <hr class="bg-gray-300 my-6">


                                <div class="flex justify-start items-center mt-6">

                                    <div class="font-medium text-xl">Deductions</div>

                                    <button type="button" class="button primary sm no-text ml-3"
                                    @click="add_deduction()">
                                        <i class="fal fa-plus"></i>
                                    </button>

                                </div>

                                <div class="grid grid-cols-5 mt-4">

                                    <div class="col-span-4">

                                        <div class="deductions">

                                            @if($deductions)

                                                @foreach($deductions as $deduction)

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
                                                                class="form-element input md numbers-only money-decimal commission-input required"
                                                                name="amount[]"
                                                                data-label="Amount"
                                                                value="{{ $deduction -> amount }}">
                                                            </div>

                                                            <div class="col-span-1 sm:col-span-2 md:col-span-3 mr-2 mb-2">
                                                                <input
                                                                type="text"
                                                                class="form-element input md required"
                                                                name="description[]"
                                                                data-label="Description"
                                                                value="{{ $deduction -> description }}">
                                                            </div>

                                                            <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                                                                <select
                                                                class="form-element select md required"
                                                                name="paid_to[]"
                                                                data-label="Paid To"
                                                                @change="show_other = false; if($el.value == 'Other') { show_other = true }">
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
                                                                    class="form-element input md required"
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

                                                @endforeach

                                            @endif

                                        </div>

                                    </div>

                                    <div class="col-span-1 ml-8 whitespace-nowrap place-self-end">

                                        <div class="text-lg text-right bg-red-50 text-red-800 p-4 rounded-md">
                                            <span id="deductions_amount">$0.00</span>
                                        </div>

                                    </div>

                                </div>

                                <hr class="bg-gray-300 my-6">

                                <div class="grid grid-cols-5">

                                    <div class="col-span-4 flex items-center font-medium text-xl">Net Commission</div>

                                    <div class="col-span-1 ml-8 whitespace-nowrap">

                                        <div class="text-right bg-green-50 text-green-800 p-4 rounded-md">
                                            <span id="net_commission_amount">$0.00</span>
                                        </div>

                                    </div>

                                </div>

                                <hr class="bg-gray-300 my-6">

                                <div class="font-medium text-xl">Commissions Out</div>

                                <div class="grid grid-cols-5 mt-6">

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

                                                    <div class="flex jusify-start items-center">

                                                        <div class="mr-4">
                                                            <a href="javascript:void(0)" class="block" @click="show_details = !show_details">
                                                                <i :class="{ 'fas fa-plus-circle fa-lg text-yellow-600': show_details === false, 'fas fa-times-circle fa-lg text-red-700': show_details === true }"></i>
                                                            </a>
                                                        </div>

                                                        <div class="text-lg text-gray-800">{{ $loan_officer -> fullname ?? 'Loan Officer' }}</div>

                                                    </div>

                                                    <div class="col-span-1 whitespace-nowrap">

                                                        <div class="text-right bg-red-50 text-red-800 p-2 rounded-md">
                                                            <span id="loan_officer_{{ $index }}_commission_amount_ele">$0.00</span>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div x-show="show_details" x-transition>

                                                    <nav class="flex space-x-4 my-4 items-center border-t pt-3" aria-label="Tabs">

                                                        <div class="mr-6">Calculate By</div>

                                                        <a href="javascript:void(0)" class=" px-3 py-2 font-medium text-sm rounded-md"
                                                        @click="active_commission_tab = '1';
                                                        if(index == 1) {
                                                            loan_officer_1_commission_type = 'commission';
                                                        } else if(index == 2) {
                                                            loan_officer_2_commission_type = 'commission';
                                                        }
                                                        document.querySelector('#loan_officer_'+index+'_commission_type').value = 'commission';
                                                        total_commission();"
                                                        :class="{ 'bg-primary-lightest text-primary-dark': active_commission_tab === '1', 'text-gray-500 hover:text-gray-700': active_commission_tab !== '1' }">
                                                            Commission
                                                        </a>

                                                        <a href="javascript:void(0)" class=" px-3 py-2 font-medium text-sm rounded-md"
                                                        @click="active_commission_tab = '2';
                                                        if(index == 1) {
                                                            loan_officer_1_commission_type = 'loan_amount';
                                                        } else if(index == 2) {
                                                            loan_officer_2_commission_type = 'loan_amount';
                                                        }
                                                        document.querySelector('#loan_officer_'+index+'_commission_type').value = 'loan_amount';
                                                        total_commission()"
                                                        :class="{ 'bg-primary-lightest text-primary-dark': active_commission_tab === '2', 'text-gray-500 hover:text-gray-700': active_commission_tab !== '2' }">
                                                            Loan Amount
                                                        </a>
                                                    </nav>


                                                    <div class="mt-6">

                                                        <div x-show="active_commission_tab === '1'">

                                                            <div class="text-sm mb-2">Commission Percent</div>

                                                            <div class="flex justify-start items-center">

                                                                <div class="w-24">
                                                                    @php
                                                                    $commission_percent = $loan_officer -> commission_percent;
                                                                    if($index == '1') {
                                                                        if($loan -> loan_officer_1_commission_percent) {
                                                                            $commission_percent = $loan -> loan_officer_1_commission_percent;
                                                                        }
                                                                    } else if($index == '2') {
                                                                        if($loan -> loan_officer_2_commission_percent) {
                                                                            $commission_percent = $loan -> loan_officer_2_commission_percent;
                                                                        }
                                                                    }
                                                                    @endphp
                                                                    <input
                                                                    type="text"
                                                                    class="form-element input md numbers-only no-decimals text-center commission-input required"
                                                                    name="loan_officer_{{ $index }}_commission_percent"
                                                                    data-label=""
                                                                    value="{{ $commission_percent }}">


                                                                </div>

                                                                <div><i class="fal fa-percentage ml-1 fa-lg text-gray-500"></i></div>

                                                                <div class="ml-4">
                                                                    of <span class="net-commission-amount ml-3"></span>
                                                                </div>

                                                                <div class="ml-4">
                                                                    = <span class="loan-officer-{{ $index }}-commission-amount ml-3 text-lg font-bold"></span>
                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div x-show="active_commission_tab === '2'">

                                                            <div class="flex justify-start items-center mb-3 bg-primary-lightest text-primary-dark p-2 rounded-md">
                                                                <div id="loan_officer_{{ $index }}_loan_amount_alert_icon"></div>
                                                                <div id="loan_officer_{{ $index }}_loan_amount_alert_text"></div>
                                                            </div>

                                                            <div class="flex justify-start items-center">
                                                                <div class="leading-loose" id="loan_officer_{{ $index }}_loan_amount_details"></div>
                                                            </div>

                                                        </div>

                                                        @php
                                                        $loan_amount_percent = $loan_officer -> loan_amount_percent;
                                                        if($index == '1') {
                                                            if($loan -> loan_officer_1_loan_amount_percent) {
                                                                $loan_amount_percent = $loan -> loan_officer_1_loan_amount_percent;
                                                            }
                                                        } else if($index == '2') {
                                                            if($loan -> loan_officer_2_loan_amount_percent) {
                                                                $loan_amount_percent = $loan -> loan_officer_2_loan_amount_percent;
                                                            }
                                                        }
                                                        @endphp
                                                        <input type="hidden" name="loan_officer_{{ $index }}_loan_amount_percent" value="{{ $loan_amount_percent }}">

                                                        <input type="hidden" class="commission-paid-out" name="loan_officer_{{ $index }}_commission_amount" id="loan_officer_{{ $index }}_commission_amount">

                                                        <input type="hidden" name="loan_officer_{{ $index }}_commission_type" id="loan_officer_{{ $index }}_commission_type">

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

                                                                @forelse($loan_officer_deductions -> where('lo_index', $index) as $loan_officer_deduction)

                                                                    <div class="flex justify-between items-end loan-officer-{{ $index }}-deduction">

                                                                        <div class="grid grid-cols-1 sm:grid-cols-3">

                                                                            <div class="mr-2 mb-2">
                                                                                <input
                                                                                type="text"
                                                                                class="form-element input md numbers-only money-decimal commission-input required"
                                                                                name="loan_officer_deduction_amount[]"
                                                                                data-label="Amount"
                                                                                value="{{ $loan_officer_deduction -> amount }}">
                                                                            </div>

                                                                            <div class="col-span-2 mb-2">
                                                                                <input
                                                                                type="text"
                                                                                class="form-element input md required"
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

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach


                                    </div>

                                    <div class="col-span-1 ml-8 whitespace-nowrap place-self-end">

                                        <div class="text-lg text-right bg-red-50 text-red-800 p-4 rounded-md">
                                            <span id="commissions_paid_amount">$0.00</span>
                                        </div>

                                    </div>

                                </div>




                                <hr class="bg-gray-300 my-6">

                                <div class="p-8 flex justify-around">
                                    <button type="button" class="button primary xl" @click="save_commission($el)"><i class="fal fa-check mr-3"></i> Save Commission</button>
                                </div>

                                <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                            </div>

                            <div class="col-span-4 lg:col-span-1">

                                <div class="sticky top-12">

                                    <div class="bg-green-50 ml-0 lg:ml-8 rounded-lg">

                                        a sdfasd fajsdf asfdf dsa

                                    </div>

                                </div>

                            </div>

                        </div>


                    </form>

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
                    class="form-element input md numbers-only money-decimal commission-input required"
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
                        class="form-element input md numbers-only money-decimal commission-input required"
                        name="amount[]"
                        data-label="Amount">
                    </div>

                    <div class="col-span-1 sm:col-span-2 md:col-span-3 mr-2 mb-2">
                        <input
                        type="text"
                        class="form-element input md required"
                        name="description[]"
                        data-label="Description">
                    </div>

                    <div class="col-span-1 md:col-span-2 mr-2 mb-2">
                        <select
                        class="form-element select md required"
                        name="paid_to[]"
                        data-label="Paid To"
                        @change="show_other = false; if($el.value == 'Other') { show_other = true }">
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
                            class="form-element input md required"
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
                        class="form-element input md numbers-only money-decimal commission-input required"
                        name="loan_officer_deduction_amount[]"
                        data-label="Amount">
                    </div>

                    <div class="col-span-2 mb-2">
                        <input
                        type="text"
                        class="form-element input md required"
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
