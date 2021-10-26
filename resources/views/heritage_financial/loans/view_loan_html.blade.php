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
    x-data="loan()">

        <div class="max-w-1000-px mx-auto pt-8 md:pt-12 lg:pt-16 px-4">


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

                <div x-show="active_tab === '1'" class="pt-4 sm:pt-12">

                    <form id="details_form">

                        <div class="font-medium text-gray-700 border-b mb-2"><i class="fad fa-users mr-2"></i> People</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

                            <div class="col-span-1 m-2 sm:m-3">
                                <select
                                class="form-element select md required"
                                id="loan_officer_id"
                                name="loan_officer_id"
                                data-label="Loan Officer">
                                    <option value=""></option>
                                    @foreach($loan_officers -> where('emp_position', 'loan_officer') as $loan_officer)
                                    <option value="{{ $loan_officer -> id }}" @if($loan && $loan -> loan_officer_id == $loan_officer -> id) selected @endif>{{ $loan_officer -> last_name }}, {{ $loan_officer -> first_name }}</option>
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
                                    @foreach($loan_officers -> where('emp_position', 'loan_officer') as $loan_officer)
                                    <option value="{{ $loan_officer -> id }}" @if($loan && $loan -> loan_officer_2_id == $loan_officer -> id) selected @endif>{{ $loan_officer -> last_name }}, {{ $loan_officer -> first_name }}</option>
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
                                    @foreach($loan_officers -> whereIn('emp_position', ['processor', 'manager']) as $loan_officer)
                                    <option value="{{ $loan_officer -> id }}" @if($loan && $loan -> processor_id == $loan_officer -> id) selected @endif>{{ $loan_officer -> last_name }}, {{ $loan_officer -> first_name }}</option>
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
                                x-on:keyup="get_location_details('#loan_form', '', '#zip', '#city', '#state');">
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


                        <div class="pl-4">

                            <div class="flex justify-between items-center font-medium mt-7">
                                <div class="">Checks In</div>
                                <div class="text-lg border border-green-600 bg-green-50 text-green-800 p-2 rounded-md">
                                    <span id="commission_check_amount_ele">$0.00</span>
                                </div>
                            </div>

                            <div class="m-2 sm:m-3 w-60">
                                <input
                                type="text"
                                class="form-element input md bg-green-50 numbers-only money-decimal commission-input required"
                                id="commission_check_amount"
                                name="commission_check_amount"
                                data-label="Commission Check Amount"
                                value="{{ $loan -> commission_check_amount ?? null }}"
                                @keyup="set_commission_check_amount();">
                            </div>


                            <div class="flex justify-between items-center font-medium mt-7 mb-3">
                                <div class="">Deductions</div>
                                <div class="text-lg border border-red-600 bg-red-50 text-red-800 p-2 rounded-md">
                                    - <span id="deductions_amount">$0.00</span>
                                </div>
                            </div>

                            <div class="deductions ml-3">

                                @if($deductions)

                                    @foreach($deductions as $deduction)

                                        @php
                                        $non_other = ['Company', 'Loan Officer 1', 'Loan Officer 2'];
                                        $show_other = null;
                                        if(!in_array($deduction -> paid_to, $non_other)) {
                                            $show_other = 'yes';
                                        }
                                        @endphp

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-10"
                                        x-ref="deduction"
                                        x-data="{ show_other: @if($show_other) true @else false @endif }">

                                            <div class="col-span-2 m-1">
                                                <input
                                                type="text"
                                                class="form-element input md bg-red-50 numbers-only money-decimal commission-input required"
                                                name="amount[]"
                                                data-label="Amount"
                                                value="{{ $deduction -> amount }}">
                                            </div>

                                            <div class="col-span-1 sm:col-span-2 md:col-span-3 m-1">
                                                <input
                                                type="text"
                                                class="form-element input md required"
                                                name="description[]"
                                                data-label="Description"
                                                value="{{ $deduction -> description }}">
                                            </div>

                                            <div class="col-span-1 md:col-span-2 m-1">
                                                <select
                                                class="form-element select md required"
                                                name="paid_to[]"
                                                data-label="Paid To"
                                                @change="show_other = false; if($el.value == 'Other') { show_other = true }">
                                                    <option value=""></option>
                                                    <option value="Company" @if($deduction -> paid_to == 'Company') selected @endif>Company</option>
                                                    <option value="Loan Officer 1" @if($deduction -> paid_to == 'Loan Officer 1') selected @endif>Loan Officer 1</option>
                                                    <option value="Loan Officer 2" @if($deduction -> paid_to == 'Loan Officer 2') selected @endif>Loan Officer 2</option>
                                                    <option value="Other" @if($show_other) selected @endif>Other</option>
                                                </select>
                                            </div>

                                            <div class="col-span-1 md:col-span-2 m-1">
                                                <div x-show="show_other">
                                                    <input
                                                    type="text"
                                                    class="form-element input md required"
                                                    name="paid_to_other[]"
                                                    data-label="Paid To Name"
                                                    value="{{ $deduction -> paid_to }}">
                                                </div>
                                            </div>

                                            <div class="col-span-1 m-1 place-self-end">
                                                <button type="button" class="button danger md no-text"
                                                @click.prevent="$refs.deduction.remove(); total_commission();">
                                                    <i class="fal fa-times"></i>
                                                </button>
                                            </div>

                                        </div>

                                    @endforeach

                                @endif

                            </div>

                            <div class="mt-4 ml-4">
                                <button type="button" class="button primary sm"
                                @click="add_deduction()">
                                    <i class="fal fa-plus mr-2"></i> Add Deduction
                                </button>
                            </div>

                            <hr class="bg-gray-300 my-6">

                            <div class="flex justify-between items-center font-medium mt-7">
                                <div class="text-lg">Net Commission</div>
                                <div class="text-xl border border-green-600 bg-green-50 text-green-800 p-2 rounded-md">
                                    <span id="net_commission_amount">$0.00</span>
                                </div>
                            </div>

                            <hr class="bg-gray-300 my-6">


                            <div class="flex justify-between items-center font-medium mt-7 mb-3">
                                <div class="">Commissions Paid</div>
                                <div class="text-lg border border-red-600 bg-red-50 text-red-800 p-2 rounded-md">
                                    - <span id="commissions_paid_amount">$0.00</span>
                                </div>
                            </div>




                        </div>

                        <hr class="bg-gray-300 my-6">

                        <div class="p-8 flex justify-around">
                            <button type="button" class="button primary xl" @click="save_commission($el)"><i class="fal fa-check mr-3"></i> Save Commission</button>
                        </div>

                        <input type="hidden" name="uuid" value="{{ $loan -> uuid ?? null }}">

                    </form>

                </div>


                <div x-show="active_tab === '3'" class="pt-4 sm:pt-12">

                </div>

            </div>

        </div>

        <template id="deduction_template">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-10"
            x-ref="deduction"
            x-data="{ show_other: false }">

                <div class="col-span-2 m-1">
                    <input
                    type="text"
                    class="form-element input md bg-red-50 numbers-only money-decimal commission-input required"
                    name="amount[]"
                    data-label="Amount">
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-3 m-1">
                    <input
                    type="text"
                    class="form-element input md required"
                    name="description[]"
                    data-label="Description">
                </div>

                <div class="col-span-1 md:col-span-2 m-1">
                    <select
                    class="form-element select md required"
                    name="paid_to[]"
                    data-label="Paid To"
                    @change="show_other = false; if($el.value == 'Other') { show_other = true }">
                        <option value=""></option>
                        <option value="Company">Company</option>
                        <option value="Loan Officer 1">Loan Officer 1</option>
                        <option value="Loan Officer 2">Loan Officer 2</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-span-1 md:col-span-2 m-1">
                    <div x-show="show_other">
                        <input
                        type="text"
                        class="form-element input md required"
                        name="paid_to_other[]"
                        data-label="Paid To Name">
                    </div>
                </div>

                <div class="col-span-1 m-1 place-self-end">
                    <button type="button" class="button danger md no-text"
                    @click.prevent="$refs.deduction.remove(); total_commission()">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

            </div>

        </template>

    </div>


</x-app-layout>
