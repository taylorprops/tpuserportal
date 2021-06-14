

@if($level == '1')
    <a href="{{ $link }}" class="group flex items-center text-gray-300 hover:bg-default-light hover:text-white px-2 py-2 text-sm font-medium focus:bg-default-light">
        <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
            <i class="{{ $icon }}"></i>
        </div>
        {{ $title }}
    </a>
@endif


@if($level == '2')

    <div x-data="{ sub_menu: false }" class="nav-link w-full">

        <div class="cursor-pointer text-gray-300 hover:bg-default-light hover:text-white group flex items-center px-2 py-2 text-sm font-medium focus:bg-default-light"
        @click="hide_menus(); sub_menu = true;"
        :class="{ 'bg-default-light' : sub_menu === true }">

            <div class="flex justify-between items-center w-full">

                <div class="flex justify-start items-center">
                    <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                    {{ $title }}
                </div>
                <div>
                    <i class="fal fa-angle-right text-gray-300 fa-lg" :class="{ '' : !sub_menu, 'fa-rotate-90' : sub_menu }"></i>
                </div>
            </div>

        </div>

        <div class="space-y-1 bg-default-light" x-show.transition="sub_menu">

            @foreach($level2 as $link)
                <a href="{{ $link['link'] }}" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                </a>
            @endforeach

        </div>


    </div>

@endif


@if($level == '3')

    <div x-data="{ sub_menu: false }" class="nav-link w-full">

        <div class="cursor-pointer text-gray-300 hover:bg-default-light hover:text-white group flex items-center px-2 py-2 text-sm font-medium focus:bg-default-light"
        @click="hide_menus(); sub_menu = true"
        :class="{ 'bg-default-light' : sub_menu === true }">

            <div class="flex justify-between items-center w-full">

                <div class="flex justify-start items-center">
                    <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                    {{ $title }}
                </div>
                <div>
                    <i class="fal fa-angle-right text-gray-300 fa-lg" :class="{ '' : !sub_menu, 'fa-rotate-90' : sub_menu }"></i>
                </div>
            </div>

        </div>

        <div class="space-y-1 bg-default-light" x-show.transition="sub_menu">

            @foreach($level3 as $link)

                @if(!isset($link['sub_links']))

                    <a href="{{ $link['link'] }}" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                        <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                    </a>

                @else

                    <div x-data="{ sub_menu_2: false }" class="nav-link w-full">

                        <div class="cursor-pointer w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300"
                        @click="sub_menu_2 = true">

                            <div class="flex justify-between items-center w-full">

                                <div class="flex justify-start items-center">
                                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                                </div>
                                <div>
                                    <i class="fal fa-angle-right text-gray-300" :class="{ '' : !sub_menu_2, 'fa-rotate-90' : sub_menu_2 }"></i>
                                </div>
                            </div>

                        </div>


                        <!-- Expandable link section, show/hide based on state. -->
                        <div class="space-y-1 bg-default-light rounded pl-8" x-show.transition="sub_menu_2">
                            @foreach($link['sub_links'] as $link)
                                <a href="{{ $link['link'] }}" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                                    {{ $link['title'] }}
                                </a>
                            @endforeach

                        </div>

                    </div>

                @endif

            @endforeach

        </div>


    </div>

@endif
