

@if($level == '1')
    <a href="{{ $link }}" class="group flex items-center text-gray-300 hover:bg-default-light hover:text-white px-2 py-2 text-sm font-medium focus:bg-default-light">
        <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
            <i class="{{ $icon }}"></i>
        </div>
        {{ $title }}
    </a>
@endif


@if($level == '2')

    <div x-data="{ show_sub_menu: false }" class="nav-link w-full">

        <div class="cursor-pointer text-gray-300 hover:bg-default-light hover:text-white group flex items-center px-2 py-2 text-sm font-medium focus:bg-default-light"
        @click.stop="show_sub_menu = !show_sub_menu"
        :class="{ 'bg-default-light' : show_sub_menu === true }">

            <div class="flex justify-between items-center w-full">

                <div class="flex justify-start items-center">
                    <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                    {{ $title }}
                </div>
                <div>
                    <i class="fal fa-angle-right text-gray-300 fa-lg" :class="{ '' : !show_sub_menu, 'fa-rotate-90' : show_sub_menu }"></i>
                </div>
            </div>

        </div>

        <div class="space-y-1 bg-default-light" x-show="show_sub_menu" x-transition>
            @foreach($level2 as $link)
                <a href="{{ $link['link'] }}" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                </a>
            @endforeach

        </div>


    </div>

@endif


@if($level == '3')

    <div x-data="{ show_sub_menu: false }" class="nav-link w-full">

        <div class="cursor-pointer text-gray-300 hover:bg-default-light hover:text-white group flex items-center px-2 py-2 text-sm font-medium focus:bg-default-light"
        @click.stop="show_sub_menu = !show_sub_menu"
        :class="{ 'bg-default-light' : show_sub_menu === true }">

            <div class="flex justify-between items-center w-full">

                <div class="flex justify-start items-center">
                    <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                    {{ $title }}
                </div>
                <div>
                    <i class="fal fa-angle-right text-gray-300 fa-lg" :class="{ '' : !show_sub_menu, 'fa-rotate-90' : show_sub_menu }"></i>
                </div>
            </div>

        </div>

        <div class="space-y-1 bg-default-light" x-show="show_sub_menu" x-transition>

            @foreach($level3 as $link)

                @if(!isset($link['sub_links']))

                    <a href="{{ $link['link'] }}" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                        <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                    </a>

                @else

                    <div x-data="{ show_sub_menu_2: false }" class="nav-link w-full">

                        <div class="cursor-pointer w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300"
                        @click.stop="show_sub_menu_2 = !show_sub_menu_2">

                            <div class="flex justify-between items-center w-full">

                                <div class="flex justify-start items-center">
                                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                                </div>
                                <div>
                                    <i class="fal fa-angle-right text-gray-300" :class="{ '' : !show_sub_menu_2, 'fa-rotate-90' : show_sub_menu_2 }"></i>
                                </div>
                            </div>

                        </div>


                        <!-- Expandable link section, show/hide based on state. -->
                        <div class="space-y-1 bg-default-light rounded pl-8" x-show="show_sub_menu_2" x-transition>
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
