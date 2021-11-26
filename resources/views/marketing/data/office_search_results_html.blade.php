
@forelse($offices as $office)

    @if(count($office -> agents) > 0)

        <div class="p-2 border-b text-sm">

            <div class="grid grid-cols-11 gap-2">

                <div class="col-span-1">
                    <input type="checkbox" class="form-element checkbox xl primary" data-office-mls-id="{{ $office -> OfficeMlsId }}" checked>
                </div>

                <div class="col-span-4">
                    {{ $office -> OfficeName }}
                </div>

                <div class="col-span-4">
                    {{ $office -> OfficeAddress1 }} {{ $office -> OfficeCity }}, {{ $office -> OfficeStateOrProvince }} {{ $office -> OfficePostalCode }}
                </div>

                <div class="col-span-1">
                    {{ $office -> OfficeMlsId }}
                </div>

                <div class="col-span-1">
                    {{ count($office -> agents) }} Agents
                </div>

            </div>

        </div>

    @endif

@empty
    <div class="text-gray-400 font-semibold text-xl">No Records Found</div>
@endforelse
