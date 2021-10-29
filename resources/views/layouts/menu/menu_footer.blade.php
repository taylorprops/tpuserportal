

<div class="grid grid-cols-4 w-full pt-3">
    <div class="col-span-1 flex justify-around items-center">
        <img class="inline-block h-12 w-12 rounded-full" src="{{ auth() -> user() -> photo_location_url }}" alt="">
    </div>
    <div class="ml-3 col-span-3">
        <div class="text-sm text-gray-700 border-b pb-1 mb-1">
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
