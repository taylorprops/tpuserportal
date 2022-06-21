@php
$title = 'Reports';
$breadcrumbs = [[$title]];
@endphp
<x-app-layout>
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="reports()">

        <div class="w-full mx-auto sm:px-6 lg:px-12">

            <div class="mt-16">

                <div>

                    <div class="sm:hidden">

                        <label for="tabs" class="sr-only">Select Report Type</label>
                        <select id="tabs" name="tabs"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                            @change="active_tab = $el.value">
                            <option value="1" selected>Mortgage</option>
                            <option value="2">Real Estate</option>
                            <option value="3">Title</option>
                        </select>

                    </div>

                    <div class="hidden sm:block">

                        <div class="border-b border-gray-200">

                            <nav class="-mb-px flex space-x-16" aria-label="Tabs">

                                <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 1"
                                    :class="{
                                        'border-primary-light text-primary': active_tab ===
                                            1,
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 1
                                    }">
                                    Mortgage
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 2"
                                    :class="{
                                        'border-primary-light text-primary': active_tab ===
                                            2,
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 2
                                    }">
                                    Real Estate
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 3"
                                    :class="{
                                        'border-primary-light text-primary': active_tab ===
                                            3,
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 3
                                    }">
                                    Title
                                </a>

                            </nav>

                        </div>

                    </div>


                    <div x-show="active_tab === 1" x-transition>

                        <div class="pt-12">


                            <div class="mb-12">

                                <div class="sm:hidden">
                                    <label for="tabs" class="sr-only">Select a tab</label>
                                    <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
                                    <select id="tabs" name="tabs" class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
                                        @change="active_mortgage_tab = $el.value">
                                        <option value="1">Meeting Reports</option>
                                        <option value="2">Detailed Report</option>
                                    </select>
                                </div>

                                <div class="hidden sm:block">

                                    <nav class="flex space-x-4" aria-label="Tabs">

                                        <a href="#" class="text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm rounded-md"
                                            @click="active_mortgage_tab = 1"
                                            :class="{
                                                'bg-primary-lightest text-primary': active_mortgage_tab === 1,
                                                'text-gray-500 bg-white hover:text-gray-700': active_mortgage_tab !==
                                                    1
                                            }">
                                            Meeting Reports
                                        </a>

                                        <a href="#" class="text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm rounded-md"
                                            @click="active_mortgage_tab = 2"
                                            :class="{
                                                'bg-primary-lightest text-primary': active_mortgage_tab === 2,
                                                'text-gray-500 bg-white hover:text-gray-700': active_mortgage_tab !==
                                                    2
                                            }">
                                            Detailed Report
                                        </a>

                                    </nav>

                                </div>

                            </div>

                            <div x-show="active_mortgage_tab === 1" x-transition>

                                <div class="max-w-700-px">

                                    <div class="flex flex-col">

                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 w-full">

                                            <div class="py-2 align-middle inline-block sm:px-6 lg:px-8 w-full">

                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg w-full">

                                                    <table class="w-full">

                                                        <tbody class="bg-white divide-y divide-gray-200">

                                                            @php
                                                                $reports = ['loans_in_process_by_loan_officer', 'closed_loans_by_month', 'closed_loans_by_month_detailed', 'closed_loans_by_loan_officer'];
                                                            @endphp
                                                            @foreach ($reports as $report)
                                                                <tr>
                                                                    <td class="p-2">
                                                                        <div class="flex justify-around items-center">
                                                                            <input type="checkbox" class="form-element checkbox success md report-checkbox"
                                                                                data-report="{{ $report }}" @click="show_print_all_button()">
                                                                        </div>
                                                                    </td>
                                                                    <td class="p-2">
                                                                        {{ ucwords(str_replace('_', ' ', $report)) }}
                                                                    </td>
                                                                    <td class="p-2 w-28">
                                                                        <button type="button" class="button primary md" @click="print_report($el, '{{ $report }}')">
                                                                            <i class="fad fa-print mr-2"></i> Print
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div x-show="active_mortgage_tab === 2" x-transition>

                                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                                    <div class="col-span-1 lg:col-span-2 flex flex-col">

                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                                            <div class="py-2 align-middle inline-block sm:px-6 lg:px-8">

                                                <div class="mb-3"><i class="fa fa-search mr-2 text-gray-500"></i> <span class="text-lg text-gray-700">Search</span></div>

                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                                                    <div class="w-full p-4">

                                                        <form id="detailed_report_form" x-ref="detailed_report_form">

                                                            <div class="grid grid-cols-2 gap-4">

                                                                <div class="col-span-2">
                                                                    <div class="flex justify-start items-end space-x-2">
                                                                        <div>
                                                                            <input type="date" class="form-element input md" name="settlement_date_start"
                                                                                data-label="Settlement Date">
                                                                        </div>
                                                                        <div> to </div>
                                                                        <div>
                                                                            <input type="date" class="form-element input md" name="settlement_date_end">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <select class="form-element select md" name="lender_uuid" data-label="Lender">
                                                                        <option value=""></option>
                                                                        @foreach ($lenders as $lender)
                                                                            <option value="{{ $lender -> uuid }}">{{ $lender -> company_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>


                                                                <div>
                                                                    <select class="form-element select md" name="state" data-label="State">
                                                                        <option value=""></option>
                                                                        @foreach ($states as $state)
                                                                            <option value="{{ $state }}">{{ $state }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <select class="form-element select md" name="loan_type" data-label="Loan Type">
                                                                        <option value=""></option>
                                                                        <option value="Conventional">Conventional</option>
                                                                        <option value="FHA">FHA</option>
                                                                        <option value="VA">VA</option>
                                                                        <option value="USDA">USDA</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <select class="form-element select md" name="loan_purpose" data-label="Loan Purpose">
                                                                        <option value=""></option>
                                                                        <option value="purchase">Purchase</option>
                                                                        <option value="refi">Refinance</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <select class="form-element select md" name="mortgage_type" data-label="Mortgage Type">
                                                                        <option value=""></option>
                                                                        <option value="first">First</option>
                                                                        <option value="second">Second</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <select class="form-element select md" name="reverse" data-label="Reverse Mortgage">
                                                                        <option value=""></option>
                                                                        <option value="yes">Yes</option>
                                                                        <option value="no">No</option>
                                                                    </select>
                                                                </div>

                                                            </div>

                                                            <div class="flex justify-around p-4 mt-4">
                                                                <button type="button" class="button primary lg" x-ref="search_button" @click="get_detailed_report($el)">
                                                                    Search <i class="fal fa-search ml-2"></i>
                                                                </button>
                                                            </div>

                                                            <input type="hidden" name="report_type" x-ref="report_type" value="">

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-span-1 lg:col-span-3 flex flex-col">

                                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                                            <div class="py-2 align-middle inline-block sm:px-6 lg:px-8 w-full">

                                                <div class="mb-3"><i class="fa fa-info-square mr-2 text-gray-500"></i> <span class="text-lg text-gray-700">Results
                                                        Details</span></div>

                                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg w-full">

                                                    <div class="w-full p-4">
                                                        <div x-ref="results_div_details"></div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>



                                {{-- Results --}}
                                <div class="mt-12"><i class="fa fa-table mr-2 text-gray-500"></i> <span class="text-lg text-gray-700">Results Data</span></div>
                                <div x-ref="results_div_data" class="max-w-1400-px"></div>

                            </div>

                        </div>

                    </div>

                    <div x-show="active_tab === 2" x-transition> No Data </div>

                    <div x-show="active_tab === 3" x-transition> No Data </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
