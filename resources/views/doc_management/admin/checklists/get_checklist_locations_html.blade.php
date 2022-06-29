<ul class="w-full border border-gray-100 rounded animate__animated animate__fadeIn">

    @foreach ($checklist_locations as $checklist_location)
        {{-- blade-formatter-disable --}}
        @php
            $checklist_location_id = $checklist_location -> id;
        @endphp
{{-- blade-formatter-enable --}}

        <li class="form-group-li border border-b p-3 w-full" data-id="{{ $checklist_location_id }}" x-show="location_id === '{{ $checklist_location_id }}'"
            :class="{ 'active': location_id === '{{ $checklist_location_id }}' }">

            <div class="h-screen-85 overflow-auto" id="checklist_location_{{ $checklist_location_id }}">

            </div>

        </li>
    @endforeach

</ul>
