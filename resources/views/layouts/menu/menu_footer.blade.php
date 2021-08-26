<div class="flex items-center">
    <div>
        <img class="inline-block h-10 w-10 rounded-full" src="" alt="">
    </div>
    <div class="ml-3">
        <p class="text-base font-medium text-white">
            Tom Cook
        </p>
        <p class="text-sm font-medium text-gray-400 group-hover:text-gray-300">
            <a href="">
            View profile
            </a>
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                this.closest('form').submit();">
                Log Out
            </a>
        </form>
    </div>
</div>
