
<div class="absolute top-0 left-0 z-100">
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

<div class="w-full max-w-sm sm:w-64 xl:flex xl:flex-shrink-0 h-screen fixed border-r shadow z-10" x-show="main_nav_open"
x-transition:enter="transition ease-in-out duration-300"
x-transition:enter-start="opacity-0 transform scale-x-0 -translate-x-1/2"
x-transition:enter-end="opacity-100 transform scale-x-100 translate-x-0"
x-transition:leave="transition ease-in-out duration-300"
x-transition:leave-start="opacity-100 transform scale-x-100 translate-x-0"
x-transition:leave-end="opacity-0 transform scale-x-0 -translate-x-1/2">

    <div class="flex flex-col w-full">

        <div class="flex flex-col h-0 flex-1">

            <div class="flex-1 flex flex-col pt-3 bg-gray-50">

                {{-- <div class="hidden xl:flex xl:justify-center xl:items-center h-10 pl-8"> --}}
                <div class="flex justify-center items-center h-10 pl-8">
                    <div class="w-3/4">
                        <img class="h-6" src="/images/logo/all_horizontal.svg" alt="Taylor Properties">
                    </div>
                </div>

                @php $no_access = ['loan_officer']; @endphp
                @if(!in_array(auth() -> user() -> group, $no_access))
                <div class="my-2 sm:my-4 mx-1 relative">
                    <input class="main-search-input appearance-none inline-block w-full bg-white text-gray-700 border border-gray-200 rounded py-1 sm:py-3 leading-tight  focus:bg-blue-50 focus:ring-blue-100 focus:border-blue-100" type="text" placeholder="Search">
                    <i class="fal fa-search absolute top-2 sm:top-4 right-4"></i>
                </div>
                @else
                <div class="mt-4 relative"></div>
                @endif

                <nav class="navigation flex-1 pl-2 pt-3 pb-2 bg-gray-50 space-y-1 border-t"
                x-data="{ close_all: false }">

                    @include('layouts/menu/menu_'.auth() -> user() -> group)


                </nav>
            </div>

        </div>

        <div class="flex-shrink-0 flex bg-gray-100 p-0 pb-4 border-t-2">

            @include('layouts/menu/menu_footer')

        </div>

    </div>

</div>

