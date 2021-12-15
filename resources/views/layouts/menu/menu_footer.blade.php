

<div class="grid grid-cols-5 w-full py-3 border-t absolute bottom-0">

    <div class="col-span-1 flex justify-around items-center ml-2">
        @if(auth() -> user() -> photo_location_url)
        <img class="inline-block h-14 w-12 rounded-full" src="{{ auth() -> user() -> photo_location_url }}" alt="">
        @else
        <div class="rounded-full bg-primary text-white p-1 h-8 w-8 text-center">
            {{ App\Helpers\Helper::get_initials(auth() -> user() -> first_name.' '.auth() -> user() -> last_name) }}
        </div>
        @endif
    </div>

    <div class="ml-3 col-span-4">

        <div class="text-gray-700 border-b pb-1 mb-1">
            {{ auth() -> user() -> name }}
        </div>

        <div class="text-sm text-gray-600 hover:text-gray-500">
            <a href="/employees/profile">
                <i class="fad fa-user-alt mr-2"></i> View Profile
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
            class="text-sm text-gray-600 hover:text-gray-500"
                onclick="event.preventDefault();
                this.closest('form').submit();">
                <i class="fad fa-sign-out-alt mr-2"></i> Log Out
            </a>
        </form>

    </div>

</div>
