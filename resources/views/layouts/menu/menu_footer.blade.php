


<div class="flex justify-start items-center p-2">

    <div class="flex justify-around items-center ml-2">
        @if(auth() -> user() -> photo_location_url)
        <img class="inline-block h-10 w-8 rounded-full" src="{{ auth() -> user() -> photo_location_url }}" alt="">
        @else
        <div class="rounded-full bg-primary text-white p-1 h-8 w-8 text-center">
            {{ App\Helpers\Helper::get_initials(auth() -> user() -> first_name.' '.auth() -> user() -> last_name) }}
        </div>
        @endif
    </div>

    <div class="ml-4">
        <div class="text-gray-700">
            {{ auth() -> user() -> name }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
            class="text-sm text-gray-600 hover:text-gray-500"
                onclick="event.preventDefault();
                this.closest('form').submit();">
                Log Out <i class="fad fa-sign-out-alt ml-2"></i>
            </a>
        </form>
    </div>

</div>

