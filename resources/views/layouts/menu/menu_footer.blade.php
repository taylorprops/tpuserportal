

<div class="flex items-center">
    <div>
        <img class="inline-block h-14 rounded-md" src="{{ auth() -> user() -> photo_location_url }}" alt="">
    </div>
    <div class="ml-3">
        <p class="text-base font-medium text-gray-700">
            {{ auth() -> user() -> name }}
        </p>
        <p class="text-sm font-medium text-gray-500 group-hover:text-gray-300">
            <a href="/heritage_financial/loan_officers/profile">
            View profile
            </a>
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
            class="text-primary font-sm"
                onclick="event.preventDefault();
                this.closest('form').submit();">
                Log Out
            </a>
        </form>
    </div>
</div>
