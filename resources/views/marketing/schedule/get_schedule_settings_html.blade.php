
@foreach($settings as $setting)

    @if($field == 'categories')
        <div class="p-2 my-2 border-b">
            {{ $setting -> category }}
        </div>
    @elseif($field == 'mediums')
        <div class="p-2 my-2 border-b">
            {{ $setting -> medium }}
        </div>
    @endif

@endforeach
