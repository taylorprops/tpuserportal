@foreach ($checklists as $checklist)

    {{-- blade-formatter-disable --}}
    @php
    $checklist_recipient_ids = $checklist -> recipient_ids;
    $checklist_states = $checklist -> states;

    $checklist_recipient_ids = explode(',', $checklist_recipient_ids);
    $checklist_states = explode(',', $checklist_states);
    if(!is_array($states)) {
        $states = explode(',', $states);
    }
    $states_match = true;
    foreach($states as $state) {
        if(!in_array($state, $checklist_states)) {
            $states_match = false;
        }
    }
    @endphp
    {{-- blade-formatter-enable --}}
    @if ($states_match == true)
        @if (in_array($recipient_id, $checklist_recipient_ids))
            {{-- @if (count($states_diff) == 0) --}}
            <div class="my-2 p-2 border-b">
                {!! $checklist -> data !!}
            </div>

        @endif
    @else
        There are different checklist requirements for the States that you have selected<br><br>

    @endif
@endforeach
