@php
$title = 'Manager Bonuses';
$breadcrumbs = [
['Loans', '/heritage_financial/loans'],
[$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="bonuses()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="mt-16">

                <div class="border rounded-lg mb-3"
                x-data="{ year_selected: 1 }">

                    @php $c = 1; @endphp
                    @foreach($years as $year)

                        @if(count($loans -> where('year', $year)) > 0)

                            <div class="year-container"
                            data-year="{{ $year }}">

                                <button class="block w-full text-left border-b bg-gray-50 p-4 pb-2 text-xl font-semibold"
                                @click="year_selected = year_selected === {{ $c }} ? null : {{ $c }};
                                toggle_link($el, 'year', {{ $year }})">
                                    {{ $year }} <i class="fal fa-arrow-right ml-2 transform year-icon @if($c == 1) rotate-90 @endif"></i>
                                </button>

                                <div
                                x-show="year_selected === {{ $c }}"
                                x-transition
                                x-data="{ month_selected: null }">

                                    @foreach($months as $month)

                                        @if(count($loans -> where('year', $year) -> where('month', $month)) > 0)

                                            <div class="flex justify-between items-center py-2 border-b">

                                                <div class="w-full">
                                                    <a href="javascript:void(0)" class="block w-full pl-4 text-lg no-color text-gray-500"
                                                    @click="month_selected = month_selected === {{ $month }} ? null : {{ $month }};
                                                    $refs.arrow_month_{{ $year.$month }}.classList.toggle('rotate-90');">
                                                        {{ date('F', strtotime('2000-'.$month.'-01')) }} <i class="fal fa-arrow-right ml-2 transform" x-ref="arrow_month_{{ $year.$month }}"></i>
                                                    </a>
                                                </div>

                                                <div class="flex justify-end items-center mr-4"">
                                                    <button type="button" class="button primary md"
                                                    @click="print($refs.table_{{ $c }}_{{ $month }})">
                                                        <i class="fad fa-print mr-2"></i> Print
                                                    </button>

                                                    <button type="button" class="button primary md ml-4"
                                                    @click="email_ele = $refs.table_{{ $c }}_{{ $month }}; show_email_bonuses = true">
                                                        <i class="fad fa-envelope mr-2"></i> Email
                                                    </button>
                                                </div>

                                            </div>

                                            <div class="flex flex-col mx-8 mt-4 mb-6"
                                            x-show="month_selected === {{ $month }}"
                                            x-transition>
                                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
                                                        x-ref="table_{{ $c }}_{{ $month }}">
                                                            <table class="min-w-full divide-y divide-gray-200">
                                                                <thead class="bg-gray-50">
                                                                    <tr>
                                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            Close Date
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            Loan Officer
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            Address
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            Borrowers
                                                                        </th>
                                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                            Bonus Amount
                                                                        </th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody class="bg-white divide-y divide-gray-200">
                                                                    @php $bonus_total = 0; @endphp
                                                                    @foreach($loans -> where('year', $year) -> where('month', $month) as $loan)

                                                                        <tr class="bg-white">
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                                {{ $loan -> settlement_date }}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                                {{ $loan -> loan_officer_1 -> fullname ?? null }}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                                {{ $loan -> street.' '.$loan -> city.', '.$loan -> state.' '.$loan -> zip }}
                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                                {{ $loan -> borrower_fullname }}
                                                                                @if($loan -> co_borrower_fullname)
                                                                                    <br>
                                                                                    {{ $loan -> co_borrower_fullname }}
                                                                                @endif

                                                                            </td>
                                                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                                                                ${{ number_format($loan -> manager_bonus, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                        @php $bonus_total += $loan -> manager_bonus; @endphp
                                                                    @endforeach

                                                                    <tr>
                                                                        <td colspan="5" class="text-right font-semibold text-xl p-4">
                                                                            Total Bonus - ${{ number_format($bonus_total, 2) }}
                                                                        </td>
                                                                    </tr>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @endif

                                    @endforeach

                                </div>

                            </div>
                            @php $c += 1; @endphp

                        @endif

                    @endforeach

                </div>

            </div>

        </div>

        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/4'"
        :modalTitle="'Email Bonuses'"
        :modalId="'show_email_bonuses'"
        x-show="show_email_bonuses">

            <div>
                <input type="text" class="form-element input lg" x-ref="to_email" value="delia@taylorprops.com" data-label="Send To Email">
            </div>
            <button type="button" class="button primary lg mt-4"
            @click="send_email($el)">
                Send Email <i class="fa fa-share ml-2"></i>
            </button>

        </x-modals.modal>

    </div>

</x-app-layout>
