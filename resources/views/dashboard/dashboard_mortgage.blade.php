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

    <div class="pb-12 pt-2"
    x-data="dashboard('{{ $group }}')">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12 pt-4 md:pt-8 lg:pt-16">

            <div class="grid grid-cols-1 lg:grid-cols-2 max-w-1400-px gap-12 mx-auto">

                <div class="border-4 rounded-lg">

                    <div class="rounded-t-lg border-b p-3 text-lg font-semibold">
                        Active Loans
                    </div>

                    <div class="p-2">

                        @forelse($active_loans as $loan)

                            @php
                            $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                            if($loan -> co_borrower_first != '') {
                                $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                            }
                            $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
                            @endphp

                            <div class="grid grid-cols-7 p-2 mb-2 border-b text-sm">

                                <div class="col-span-1">
                                    <a href="/heritage_financial/loans/view_loan/{{ $loan -> uuid }}" class="button primary md">View</a>
                                </div>
                                <div class="col-span-2 pl-2">
                                    {!! $borrower !!}
                                </div>
                                <div class="col-span-3 pl-2">
                                    {!! $address !!}
                                </div>
                                <div class="col-span-1 -pl-2">
                                    ${{ number_format($loan -> loan_amount) }}
                                </div>

                            </div>

                        @empty

                            <div class="w-full px-4 py-12 text-gray-400 text-xl text-center">No Active Loans</div>

                        @endforelse

                    </div>

                </div>

                <div class="border-4 rounded-lg">

                    <div class="rounded-t-lg border-b p-3 text-lg font-semibold">
                        Recent Commissions
                    </div>

                    <div class="p-2">

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>

