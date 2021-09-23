
<div class="fixed z-100">
    <div class="">
        <button class="py-2 px-4 sm:py-3"
        @click="main_nav_open = !main_nav_open;"
        x-show="!main_nav_open" x-transition:enter.duration.500ms>
            <i class="fal fa-bars fa-lg text-gray-600"></i>
        </button>
        <button class="py-2 px-4 sm:py-3"
        @click="main_nav_open = !main_nav_open;"
        x-show="main_nav_open" x-transition:enter.duration.500ms>
            <i class="fal fa-sign-out-alt fa-rotate-180 text-gray-600"></i>
        </button>
    </div>
</div>

<div class=" xl:flex xl:flex-shrink-0 h-screen fixed border-r shadow z-10" x-show="main_nav_open"
x-transition:enter="transition ease-in-out duration-300"
x-transition:enter-start="opacity-0 transform scale-x-0 -translate-x-1/2"
x-transition:enter-end="opacity-100 transform scale-x-100 translate-x-0"
x-transition:leave="transition ease-in-out duration-300"
x-transition:leave-start="opacity-100 transform scale-x-100 translate-x-0"
x-transition:leave-end="opacity-0 transform scale-x-0 -translate-x-1/2">

    <div class="flex flex-col w-64 pt-8 xl:pt-0 ">

        <div class="flex flex-col h-0 flex-1">

            <div class="flex-1 flex flex-col pt-3 bg-gray-50">

                <div class="hidden xl:flex xl:justify-center pl-8">
                    <div class="w-3/4">
                        <img class="h-6" src="/images/logo/all_horizontal.svg" alt="Taylor Properties">
                    </div>
                </div>

                <div class="my-2 sm:my-4 mx-1 relative">
                    <input class="main-search-input appearance-none inline-block w-full bg-white text-gray-700 border border-gray-200 rounded py-1 sm:py-3 leading-tight  focus:bg-blue-50 focus:ring-blue-100 focus:border-blue-100" type="text" placeholder="Search">
                    <i class="fal fa-search absolute top-4 right-4"></i>
                </div>

                <nav class="navigation flex-1 px-2 pt-3 bg-gray-50 space-y-1 border-t"
                x-data="{ close_all: false }">

                @if(auth() -> user() -> group == 'admin')
                    @include('layouts/menu/menu_admin')
                @elseif(auth() -> user() -> group == 'agent')
                    @include('layouts/menu/menu_agent')
                @elseif(auth() -> user() -> group == 'loan_officer')
                    @include('layouts/menu/menu_loan_officer')
                @endif

                </nav>
            </div>

            <div class="flex-shrink-0 flex bg-gray-100 p-4 border-t-2">

                @include('layouts/menu/menu_footer')

            </div>

        </div>

    </div>

</div>

