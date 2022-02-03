
@if($print == 'true')
    <table style="font-family: Arial, Helvetica, sans; font-size: 14px">
@endif
@forelse($notes as $note)
    @php
    $date = $note -> created_at;
    if(date('Ymd') == date('Ymd', strtotime($date))) {
        $date = 'Today '.date('H:iA', strtotime($date));
    } else {
        $date = date('n/j/y H:iA', strtotime($date));
    }
    @endphp

    @if($print == 'false')

        <div class="my-2">
            <ul role="list" class="divide-y">
                <li class="py-4 border-b">
                    <div class="flex space-x-3">

                        <div>
                            {!! App\Helpers\Helper::avatar('8', $note -> user -> user_id, $note -> user -> group) !!}
                        </div>

                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium">{{ $note -> user -> name }}</h3>
                                <p class="text-xs text-gray-500">{{ $date }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{!! nl2br($note -> notes) !!}</p>
                        </div>
                    </div>
                    @if(auth() -> user() -> id == $note -> user_id)
                        <div class="flex justify-end">
                            <div>
                                <button type="button" class="button danger sm no-text" @click="delete_note($el, {{ $note -> id }})"><i class="fal fa-times"></i></button>
                            </div>
                        </div>
                    @endif
                </li>
            </ul>
        </div>

    @else

        <tr>
            <td style="border-bottom: 1px solid #ccc">{!! nl2br($note -> notes) !!}</td>
        </tr>

    @endif


@empty

    <div class="text-gray-400 font-semibold">No Notes Added</div>

@endif
@if($print == 'true')
    </table>
@endif
