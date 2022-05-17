<div class="p-12 text-2xl">

    <div class="my-6 text-4xl font-semibold">BrightMLS Details</div>

    <div class="text-base leading-relaxed">

        <div class="flex justify-start space-x-24">

            <div>
                <span class="font-semibold text-gray-600">{{ $agent -> MemberFullName }}</span>
                <br>
                {{ App\Helpers\Helper::format_phone($agent -> MemberPreferredPhone) }}
                <br>
                <a href="{{ $agent -> MemberEmail }}" target="_blank">{{ $agent -> MemberEmail }}</a>
            </div>

            <div>
                <span class="font-semibold text-gray-600">{{ $agent -> OfficeName }}</span>
                <br>
                {{ $agent -> office -> OfficeAddress1 }}
                <br>
                {{ $agent -> office -> OfficeCity }}, {{ $agent -> office -> OfficeStateOrProvince }} {{ $agent -> office -> OfficePostalCode }}
                <br>
            </div>

            <div>
                <div class="p-2 inline-block rounded bg-sky-800 text-white">Active for {{ $years_active }} years</div>
            </div>

        </div>

    </div>

    <div class="my-4 border-b h-1"></div>

    <div class="flex justify-start space-x-24">

        <div>

            <div class="my-6 text-2xl font-semibold border-b">Listings</div>

            <div class="grid grid-cols-4">

                <div class="text-gray-800 border-b-2 p-2">Year</div>
                <div class="text-gray-800 border-b-2 p-2">Sold</div>
                <div class="text-gray-800 border-b-2 p-2">Average Price</div>
                <div class="text-gray-800 border-b-2 p-2">Volume</div>

                @foreach($listings as $listing)

                    <div class="border-b p-2">{{ $listing -> year }}</div>
                    <div class="border-b p-2">{{ $listing -> total }}</div>
                    <div class="border-b p-2">${{ number_format($listing -> average) }}</div>
                    <div class="border-b p-2">${{ number_format($listing -> total_sales) }}</div>

                @endforeach

            </div>

        </div>

        <div>

            <div class="my-6 text-2xl font-semibold border-b">Contracts</div>

            <div>

                <div class="grid grid-cols-3">

                    <div class="text-gray-800 border-b-2 p-2">Year</div>
                    <div class="text-gray-800 border-b-2 p-2">Sold</div>
                    <div class="text-gray-800 border-b-2 p-2">Average Price</div>

                    @foreach($contracts as $contract)

                            <div class="border-b p-2">{{ $contract -> year }}</div>
                            <div class="border-b p-2">{{ $contract -> total }}</div>
                            <div class="border-b p-2">${{ number_format($contract -> average) }}</div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>
