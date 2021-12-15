<div>

    <div class="fixed top-0 left-0 z-100">
        <div class="h-16 flex items-center">
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

    <div class="fixed top-0 left-0 w-full max-w-sm sm:w-64 h-screen border-r shadow z-10" x-show="main_nav_open"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-x-0 -translate-x-1/2"
    x-transition:enter-end="opacity-100 transform scale-x-100 translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="opacity-100 transform scale-x-100 translate-x-0"
    x-transition:leave-end="opacity-0 transform scale-x-0 -translate-x-1/2">


        <div class="flex flex-col h-screen bg-gray-50">

            <div class="h-28">

                <div class="flex justify-center items-center h-10 pl-8 pt-3">
                    <div class="w-3/4">
                        <img class="h-6" src="/images/logo/all_horizontal.svg" alt="Taylor Properties">
                    </div>
                </div>

                @php $no_access = ['mortgage']; @endphp
                @if(!in_array(auth() -> user() -> group, $no_access))
                <div class="my-2 sm:my-2 mx-1 relative">
                    <input class="main-search-input form-element input md" type="text" placeholder="Search">
                    <i class="fal fa-search absolute top-2 sm:top-3 right-4"></i>
                </div>
                @else
                <div class="mt-4 relative"></div>
                @endif

            </div>

            <div class="flex-1 overflow-x-auto">

                <div class="h-full">

                    <nav class="navigation pl-2 pt-3 pb-2 bg-gray-50 border-t h-full"
                    x-data="{ close_all: false }">
                        @include('layouts/menu/menu_'.auth() -> user() -> group)
                    </nav>

                </div>

            </div>

            <div class="h-36">
                <div class="pb-4 bg-gray-50 flex items-center">
                    @include('layouts/menu/menu_footer')
                </div>
            </div>

        </div>



    </div>

</div>

