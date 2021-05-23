
<div x-show="main_nav_open" class="fixed inset-0 flex z-40 xl:hidden" x-ref="dialog" aria-modal="true" style="display: none;">

    <div x-show="main_nav_open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="main_nav_open = false" aria-hidden="true" style="display: none;"></div>


    <div x-show="main_nav_open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800" style="display: none;">

        <div x-show="main_nav_open" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute top-0 right-0 -mr-12 pt-2" style="display: none;">
            <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="main_nav_open = false">
                <span class="sr-only">Close sidebar</span>
                <i class="fal fa-times fa-2x text-white"></i>
            </button>
        </div>

        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex-shrink-0 flex justify-between items-center p2-4">

                <img class="h-8 w-auto" src="/images/logo/logos.png" alt="Taylor Properties">

            </div>

            <div class="my-3 mx-1">
                <input class="main-search-input appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-blue-200" type="text" placeholder="Search">
            </div>

            <nav class="mt-5 px-2 space-y-1">

                @include('layouts/menu/menu_admin')

            </nav>
        </div>
        <div class="flex-shrink-0 flex bg-gray-700 p-4">

            @include('layouts/menu/menu_footer')

        </div>
    </div>

    <div class="flex-shrink-0 w-14">
        <!-- Force sidebar to shrink to fit close icon -->
    </div>
</div>


<!-- Static sidebar for desktop -->
<div class="hidden xl:flex xl:flex-shrink-0 h-screen fixed overflow-auto" x-show="main_nav_open">
    <div class="flex flex-col w-64">
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex flex-col h-0 flex-1 bg-gray-800">
            <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center justify-between flex-shrink-0 px-4">

                    <img class="h-auto w-3/4" src="/images/logo/logos.png" alt="Taylor Properties">

                    <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="main_nav_open = !main_nav_open">
                        <span class="sr-only">Close sidebar</span>
                        <i class="fal fa-bars text-white"></i>
                    </button>

                </div>

                <div class="my-3 mx-1">
                    <input class="main-search-input appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-blue-200" type="text" placeholder="Search">
                </div>

                <nav class="mt-5 flex-1 px-2 bg-gray-800 space-y-1">

                    @include('layouts/menu/menu_admin')

                </nav>
            </div>
            <div class="flex-shrink-0 flex bg-gray-700 p-4">

                @include('layouts/menu/menu_footer')

            </div>
        </div>
    </div>
</div>

