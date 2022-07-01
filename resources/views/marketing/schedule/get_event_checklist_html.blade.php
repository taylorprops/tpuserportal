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
    $states_match = false;
    $states_diff = array_diff($checklist_states, $states);

    @endphp
    {{-- blade-formatter-enable --}}

    @if (in_array($recipient_id, $checklist_recipient_ids))
        @if (count($states_diff) == 0)
            <div class="my-2 p-2 border-b">
                {!! $checklist -> data !!}
            </div>
        @else
            There are different checklist requirements for the States that you have selected
        @endif

    @endif
@endforeach
