<x-app-layout>
    @section('title') {{ $address }} @endsection
    <x-slot name="header">
        {{ $address }}
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="grid grid-cols-5 gap-6 text-sm">

                <div class="border shadow">
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

                <div class="border shadow">
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

                <div class="border shadow col-span-3">
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

                            <div class="grid grid-cols-4 gap-6">

                                <div>
                                    <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                                        <div class="text-right">Amount In</div>
                                        <div>{{ $escrow_total_in }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                                        <div class="text-right">Amount Out</div>
                                        <div>{{ $escrow_total_out }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6 border-b mb-3 pb-2">
                                        <div class="text-right">Still Holding</div>
                                        <div>{{ $escrow_total_left }}</div>
                                    </div>
                                </div>

                                <div class="col-span-3">

                                    @if($checks)

                                    <div class="bg-gray-50 shadow-inner p-3 rounded">

                                        <div class="font-semibold text-gray-500 mb-4">Checks</div>

                                        @foreach($checks as $check)

                                            <div class="border-b mb-3 pb-2 flex justify-start items-center">

                                                <div class="w-12">
                                                    <a href="/storage/{{ $check -> file_location }}" class="text-primary font-semibold" target="_blank">View</a>
                                                </div>
                                                <div class="w-12">
                                                    {{ ucwords($check -> check_type) }}
                                                </div>
                                                <div class="w-20">
                                                    ${{ number_format($check -> amount, 0) }}
                                                </div>
                                                <div class="w-20">
                                                    {{ $check -> number }}
                                                </div>
                                                <div class="w-24">
                                                    {{ $check -> check_date }}
                                                </div>
                                                <div class="flex-grow">
                                                    {{ $check -> name }}
                                                </div>
                                                <div class="w-16">
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

                                    @endif

                                </div>

                            </div>

                        @endif

                    </div>

                </div>


                <div class="border shadow col-span-4">
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

    </div>

</x-app-layout>
