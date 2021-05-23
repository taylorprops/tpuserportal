<a href="#" class="flex-shrink-0 group block">
    <div class="flex items-center">
        <div>
            <img class="inline-block h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixqx=DKazrFRnLX&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
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
                    {{ __('Log Out') }}
                </a>
            </form>
        </div>
    </div>
</a>
