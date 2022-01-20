@php
$title = 'Reports';
$breadcrumbs = [
[$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="reports()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="mt-16">

                <div>

                    <div class="sm:hidden">

                        <label for="tabs" class="sr-only">Select Report Type</label>
                        <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" @change="active_tab = $el.value">
                            <option value="1" selected>Mortgage</option>
                            <option value="2">Real Estate</option>
                            <option value="3">Title</option>
                        </select>

                    </div>

                    <div class="hidden sm:block">

                        <div class="border-b border-gray-200">

                            <nav class="-mb-px flex space-x-16" aria-label="Tabs">

                                <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 1" :class="{ 'border-primary-light text-primary': active_tab === 1, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 1 }">
                                    Mortgage
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 2" :class="{ 'border-primary-light text-primary': active_tab === 2, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 2 }">
                                    Real Estate
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 3" :class="{ 'border-primary-light text-primary': active_tab === 3, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 3 }">
                                    Title
                                </a>

                            </nav>

                        </div>

                    </div>


                    <div x-show="active_tab === 1" x-transition>

                        <div class="pt-12">

                            <div class="text-xl font-semibold text-gray-700 mb-6">Mortgage Reports</div>

                            <div class="flex flex-col">

                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                                    <div class="py-2 align-middle inline-block sm:px-6 lg:px-8">

                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                                            <table class="divide-y divide-gray-200 min-w-600-px">

                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="p-2">
                                                            <input type="checkbox" class="form-element checkbox success lg" @click="check_all($el.checked); show_print_all_button()">
                                                        </td>
                                                        <td colspan="2" class="p-2">
                                                            <button type="button" class="button primary md"
                                                            @click="print_report($el, null);" :disabled="!show_print_all_option">
                                                                <i class="fad fa-print mr-2"></i> Print Checked
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @php
                                                    $reports = [
                                                        'loans_in_process',
                                                        'closed_loans_by_month',
                                                        'closed_loans_by_month_detailed',
                                                        /* 'closed_loans_by_loan_officer',
                                                        'closed_loans_by_loan_officer_summary' */
                                                    ];
                                                    @endphp
                                                    @foreach($reports as $report)
                                                        <tr>
                                                            <td class="p-2">
                                                                <div class="flex justify-around items-center">
                                                                    <input type="checkbox" class="form-element checkbox success md report-checkbox"
                                                                    data-report="{{ $report }}"
                                                                    @click="show_print_all_button()">
                                                                </div>
                                                            </td>
                                                            <td class="p-2">
                                                                {{ ucwords(str_replace('_', ' ', $report)) }}
                                                            </td>
                                                            <td class="p-2">
                                                                <button type="button" class="button primary md"
                                                                @click="print_report($el, '{{ $report }}')">
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

                    <div x-show="active_tab === 2" x-transition> No Data </div>

                    <div x-show="active_tab === 3" x-transition> No Data </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
