
@forelse($notes as $note)
    @php
    $date = $note -> created_at;
    if(date('Ymd') == date('Ymd', strtotime($date))) {
        $date = 'Today '.date('H:iA', strtotime($date));
    }
    @endphp
    <div class="my-2">
        <ul role="list" class="divide-y">
            <li class="py-4 border-b">
                <div class="flex space-x-3">
                    @if(auth() -> user() -> photo_location_url)
                    <img class="h-8 w-8 rounded-full" src="{{ $note -> user -> photo_location_url }}" alt="">
                    @else
                    <div class="rounded-full bg-primary text-white p-1 h-8 w-8 text-center">
                        {{ App\Helpers\Helper::get_initials(auth() -> user() -> first_name.' '.auth() -> user() -> last_name) }}
                    </div>
                    @endif

                    <div class="flex-1 space-y-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium">{{ $note -> user -> name }}</h3>
                            <p class="text-sm text-gray-500">{{ $date }}</p>
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


@empty

    <div class="text-gray-400 font-semibold">No Notes Added</div>

@endif
