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
                            <div>{{ substr($transaction -> actualClosingDate, 0, 10) }}</div>
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
                    <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                        Escrow
                    </div>
                    <div class="p-4">

                        @if($escrow)

                            <div class="grid grid-cols-3 gap-6">

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

                                <div class="col-span-2">

                                    @if($checks)

                                        @foreach($checks as $check)

                                            <div class="border-b mb-3 pb-2 flex justify-start items-center">

                                                <div>
                                                    <a href="/storage/{{ $check -> file_location }}" class="text-primary font-semibold" target="_blank">View</a>
                                                </div>
                                                <div class="w-16 ml-6">
                                                    {{ ucwords($check -> check_type) }}
                                                </div>
                                                <div>
                                                    ${{ number_format($check -> amount, 0) }}
                                                </div>

                                            </div>

                                        @endforeach

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
