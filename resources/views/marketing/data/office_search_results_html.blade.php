<div class="mt-6 max-h-96 overflow-y-auto">

    <div class="text-xs text-gray-400 mb-3">{{ count($offices) }} Offices Found</div>

    @forelse($offices as $office)

        <div x-data="{ show_agents: false }">

            <div class="flex p-2 border-b text-xs" :class="{ 'bg-gray-50': show_agents === true }">

                <div class="ml-2 w-6">
                    <input type="checkbox" class="form-element checkbox xl primary" name="offices[]" value="{{ $office -> OfficeMlsId }}" data-office-mls-id="{{ $office -> OfficeMlsId }}" checked @click="get_results();">
                </div>

                <div class="ml-2 w-48">
                    {{ $office -> OfficeName }}
                </div>

                <div class="ml-2 w-60">
                    {{ $office -> OfficeAddress1 }}<br>{{ $office -> OfficeCity }} {{ $office -> OfficeStateOrProvince }} {{ $office -> OfficePostalCode }}
                </div>

                <div class="ml-2 w-16">
                    {{ $office -> OfficeMlsId }}
                </div>

                <div class="ml-2 flex-1 text-right"
                x-data="{ link_text: 'View Agents' }">
                    {{ count($office -> agents) }} Agents <br>
                    <a href="javascript:void(0)" @click="show_agents = ! show_agents; link_text = show_agents === true ? 'Hide Agents' : 'View Agents';" x-text="link_text"></a>
                </div>

            </div>

            <div class="mt-0 p-2 bg-gray-50 text-xs max-h-200-px overflow-y-auto"
            x-show="show_agents" x-transition>

                @foreach($office -> agents as $agent)

                    <div class="grid grid-cols-10 gap-2 border-b">

                        <div class="col-span-2">
                            {{ $agent -> MemberLastName }}, {{ $agent -> MemberFirstName }}
                        </div>
                        <div class="col-span-3">
                            {{ $agent -> MemberEmail }}
                        </div>
                        <div class="col-span-3">
                            @if($agent -> MemberAddress1)
                            {{ $agent -> MemberAddress1 }}<br>
                            {{ $agent -> MemberCity }}, {{ $agent -> MemberState }} {{ $agent -> MemberPostalCode }}
                            @endif
                        </div>
                        <div class="col-span-2">
                            {{ \App\Helpers\Helper::format_phone($agent -> MemberPreferredPhone) }}
                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @empty
        <div class="text-gray-400 font-semibold text-xl">No Records Found</div>
    @endforelse

</div>
