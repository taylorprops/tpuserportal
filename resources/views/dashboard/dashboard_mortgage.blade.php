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

        <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-12 pt-4 md:pt-8 lg:pt-16">

            <div class="grid grid-cols-1 lg:grid-cols-2 max-w-1400-px gap-12 mx-auto">

                <div class="border-4 rounded-lg overflow-x-auto min-w-600-px">

                    <div class="flex justify-between items-center rounded-t-lg border-b">
                        <div class="p-3 text-lg font-semibold">Active Loans</div>
                        <div class="mr-4">
                            <a href="/heritage_financial/loans" class="button primary sm">View All</a>
                        </div>
                    </div>

                    <div class="p-2 max-h-400-px overflow-auto whitespace-nowrap">

                        @forelse($active_loans as $loan)

                            @php
                            $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                            if($loan -> co_borrower_first != '') {
                                $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                            }
                            $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
                            @endphp

                            <div class="grid grid-cols-11 p-2 mb-2 border-b text-sm">

                                <div class="col-span-2">
                                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View</a>
                                </div>
                                <div class="col-span-3 pl-2">
                                    {!! $borrower !!}
                                </div>
                                <div class="col-span-4 pl-2 overflow-hidden">
                                    {!! $address !!}
                                </div>
                                <div class="col-span-2 -pl-2">
                                    ${{ number_format($loan -> loan_amount) }}
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

                <div class="border-4 rounded-lg overflow-x-auto min-w-600-px">

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
                                <div class="col-span-4 pl-2 overflow-hidden">
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


                <div class="col-span-1 lg:col-span-2 border-4 rounded-lg">

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

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

