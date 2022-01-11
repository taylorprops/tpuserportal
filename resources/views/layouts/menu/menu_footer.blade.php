


<div class="flex justify-start items-center p-2">

    <div class="flex justify-around items-center ml-2">
        {!! App\Helpers\Helper::avatar() !!}
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

