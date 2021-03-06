<div class="fixed top-0 left-0 h-screen overflow-y-auto xl:overflow-y-none z-10 border-r shadow" @click.outside="if(screen.width < 1000) { main_nav_open = false }">

    <div class="fixed top-0 left-0 z-100">
        <div class="h-16 flex items-center">
            <button class="py-2 px-4 sm:py-3" @click="main_nav_open = !main_nav_open;" x-show="!main_nav_open" x-transition:enter.duration.500ms>
                <i class="fal fa-bars fa-lg text-gray-600"></i>
            </button>
            <button class="py-2 px-4 sm:py-3" @click="main_nav_open = !main_nav_open;" x-show="main_nav_open" x-transition:enter.duration.500ms>
                <i class="fal fa-times fa-lg text-gray-600"></i>
            </button>
        </div>
    </div>

    <div class="relative w-64 h-full border-r shadow z-10" x-show="main_nav_open" x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-x-0 -translate-x-1/2" x-transition:enter-end="opacity-100 transform scale-x-100 translate-x-0"
        x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="opacity-100 transform scale-x-100 translate-x-0"
        x-transition:leave-end="opacity-0 transform scale-x-0 -translate-x-1/2">


        <div class="flex flex-col justify-between bg-gray-50 h-full w-64">

            <div class="h-24 flex-none bg-gray-200">

                <div class="flex justify-center items-center h-10 pl-8 pt-3">
                    <div class="w-3/4">
                        <img class="h-6" src="/images/logo/all_horizontal.svg" alt="Taylor Properties">
                    </div>
                </div>

                <div class="hidden sm:block my-2 sm:mb-0 mt-4 mx-auto max-w-200-px relative">
                    <input class="main-search-input form-element input sm" type="text" placeholder="Search" @keyup.debounce="search($el)">
                    <i class="fal fa-search absolute top-2 sm:top-2 right-3"></i>
                </div>

            </div>

            <div class="flex-1 xl:overflow-y-auto pb-24 bg-gray-50">

                <div class="">

                    <nav class="navigation pl-2 pt-3 pb-2 bg-gray-50 border-t h-full" x-data="{ close_all: false }">
                        {{-- blade-formatter-disable --}}
                        @php
                            $group = auth() -> user() -> group;
                            if (auth() -> user() -> level == 'owner') {
                                $group = 'owner';
                            }
                        @endphp
{{-- blade-formatter-enable --}}
                        @include('layouts/menu/menu_'.$group)
                    </nav>

                </div>

            </div>

            <div class="h-16 flex-none bg-gray-200">
                <div>
                    @include('layouts/menu/menu_footer')
                </div>
            </div>

        </div>

    </div>

</div>
