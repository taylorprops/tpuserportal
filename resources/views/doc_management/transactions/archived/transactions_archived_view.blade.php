<x-app-layout>
    @section('title') {{ $address }} @endsection
    <x-slot name="header">
        {{ $address }}
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 text-sm">

                <div class="border shadow m-4">
                    <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                        Property
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                            <div class="text-right">Status</div>
                            <div>{{ ucwords($transaction -> status) }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                            <div class="text-right">List Date</div>
                            <div>{{ substr($transaction -> listingDate, 0, 10) }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                            <div class="text-right">Close Date</div>
                            <div>{{ $close_date }}</div>
                        </div>
                    </div>
                </div>

                <div class="border shadow m-4">
                    <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                        Agent
                    </div>
                    <div class="p-4">
                        <div class="border-b mb-3 pb-2">
                            {{ $agent -> nickname.' '.$agent -> last }}
                        </div>
                        <div class="border-b mb-3 pb-2">
                            {{ $agent -> email1 }}
                        </div>
                        <div class="border-b mb-3 pb-2">
                            {{ $agent -> cell_phone }}
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2 border shadow m-4">
                    <div class="bg-gray-50 p-3 flex justify-between items-center">
                        <div class="font-medium text-gray-700 text-lg">Escrow</div>
                        @if($transferred_from_link)
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2" role="alert">
                                <i class="fal fa-exclamation-triangle mr-2"></i> Transferred from {!! $transferred_from_link !!}
                            </div>
                        @endif
                        @if($transferred_to_link)
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2" role="alert">
                                <i class="fal fa-exclamation-triangle mr-2"></i> Transferred to {!! $transferred_to_link !!}
                            </div>
                        @endif
                    </div>
                    <div class="p-4">

                        @if($escrow)

                            <div class="flex justify-between flex-wrap border-b pb-2 mb-3">
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="text-right">Amount In</div>
                                    <div>{{ $escrow_total_in }}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="text-right">Amount Out</div>
                                    <div>{{ $escrow_total_out }}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="text-right">Still Holding</div>
                                    <div>{{ $escrow_total_left }}</div>
                                </div>
                            </div>

                            <div class="">

                                @if($checks)

                                <div class="bg-gray-50 shadow-inner p-3 rounded">

                                    <div class="font-semibold text-gray-500 mb-4">Checks</div>

                                    <div class="w-screen-75 sm:w-screen-60 md:w-full overflow-x-auto">

                                        <div class="min-w-600">

                                            @foreach($checks as $check)

                                                <div class="flex border-b mb-3 pb-2 whitespace-nowrap w-full">

                                                    <div class="w-10 mr-2">
                                                        <a href="/storage/{{ $check -> file_location }}" class="text-primary font-semibold" target="_blank">View</a>
                                                    </div>
                                                    <div class="w-8 mr-2">
                                                        {{ ucwords($check -> check_type) }}
                                                    </div>
                                                    <div class="w-12 mr-2">
                                                        ${{ number_format($check -> amount, 0) }}
                                                    </div>
                                                    <div class="w-20 mr-2 overflow-x-auto">
                                                        {{ $check -> number }}
                                                    </div>
                                                    <div class="w-20 mr-2">
                                                        {{ $check -> check_date }}
                                                    </div>
                                                    <div class="flex-grow mr-2 overflow-x-auto">
                                                        {{ $check -> name }}
                                                    </div>
                                                    <div class="w-14">
                                                        @if($check -> cleared == 'yes')
                                                            <span class="text-success">Cleared</span>
                                                        @elseif($check -> bounced == 'yes')
                                                            <span class="text-danger">Bounced</span>
                                                        @else
                                                            <span class="text-warning">Pending</span>
                                                        @endif
                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                                @endif

                            </div>

                        @endif

                    </div>

                </div>

            </div>

            <div class="border shadow m-4">
                <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                    Documents
                </div>
                <div class="p-6 text-sm">

                    @foreach($docs as $doc)

                        <div class="border-b mb-3 pb-2">
                            <div class="flex justify-start items-center">
                                <div class="my-1">
                                    <a href="/storage/{{ $doc -> file_location }}" class="view-link px-4 py-2 bg-primary text-white text-center shadow rounded-md" target="_blank">View</a>
                                </div>
                                <div class="ml-4">
                                    {{ $doc -> fileName }}
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>

        </div>

    </div>

</x-app-layout>
