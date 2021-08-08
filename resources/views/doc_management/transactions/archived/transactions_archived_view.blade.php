<x-app-layout>
    @section('title') {{ $address }} @endsection
    <x-slot name="header">
        {{ $address }}
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="grid grid-cols-4 gap-6">

                <div class="border shadow">
                    <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                        Property
                    </div>
                    <div class="p-6">
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
                    <div class="p-6">
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


                <div class="border shadow col-span-4">
                    <div class="bg-gray-50 text-gray-700 text-lg p-3 font-medium">
                        Documents
                    </div>
                    <div class="p-6 text-sm">

                        @foreach($docs as $doc)

                            <div class="border-b mb-3 pb-2">
                                <div class="flex justify-start items-center">
                                    <div>
                                        <a href="/storage/{{ $doc -> file_location }}" class="view-link px-4 py-3 bg-primary text-white text-center shadow rounded-md" target="_blank">View</a>
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
