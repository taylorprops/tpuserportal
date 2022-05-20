@forelse($notes as $note)
@php
$date = $note -> created_at;
if(date('Ymd') == date('Ymd', strtotime($date))) {
$date = 'Today '.date('g:iA', strtotime($date));
} else {
$date = date('n/j/y g:iA', strtotime($date));
}
@endphp

<div class="my-1">
    <ul role="list" class="divide-y">
        <li class="py-1 border-b">
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
                    @if($note -> read == 0)
                    <button type="button" class="" @click="delete_note($el, {{ $note -> event_id }}, {{ $note -> id }})"><i class="fa-duotone fa-times-circle text-red-500 hover:text-red-600 fa-2x"></i></button>
                    @else
                    <span class="text-success"><i class="fal fa-check mr-2"></i> Read</span>
                    @endif
                </div>
            </div>
            @else
            <div class="flex justify-end">
                <div x-data="{ show_mark_read: '{{ $note -> read == 0 ? 'yes' : 'no' }}' }">
                    <button type="button" class="button success sm" @click="mark_note_read($el, {{ $note -> event_id }}, {{ $note -> id }}); show_mark_read = 'no'" x-show="show_mark_read === 'yes'"><i class="fal fa-check mr-2"></i> Mark Read</button>
                    <span class="text-success" x-show="show_mark_read === 'no'"><i class="fal fa-check mr-2"></i> Read</span>
                </div>
            </div>
            @endif
        </li>
    </ul>
</div>


@empty

<div class="text-gray-400 font-semibold">No Notes Added</div>

@endif
