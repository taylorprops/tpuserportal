
@php
$id = time() * rand();
@endphp

@if($level === '1')

    <li>
        <a href="{{ $link }}" class="flex items-center h-full w-full px-2 py-1 text-sm font-medium rounded-md group text-primary-dark hover:text-white hover:bg-primary focus:bg-primary focus:text-white">
            <div class="flex justify-center items-center h-6 w-6 mr-4 rounded bg-white group-hover:bg-primary-light group-hover:text-white">
                <i class="{{ $icon }}"></i>
            </div>
            <span class="group-hover:text-white">{{ $title }}</span>
        </a>
    </li>


@endif


@if($level === '2')

    <li>

        <div class="px-2 py-1 text-sm font-medium cursor-pointer rounded-md group text-primary-dark hover:text-white hover:bg-primary focus:bg-primary"
        :class="{ 'bg-primary text-white': active_menu === {{ $id }}, 'bg-none text-primary-dark': active_menu !== {{ $id }} }"
        @click="if(active_menu === {{ $id }}) { active_menu = '' } else { active_menu = {{ $id }} }; active_sub_menu = '';">

            <div class="h-full w-full flex justify-between items-center rounded-md pr-3">

                <div class="flex justify-start items-center">
                    <div class="flex justify-center items-center h-6 w-6 mr-4 rounded group-hover:bg-primary-light group-hover:text-white"
                    :class="{ 'bg-primary-light text-white': active_menu === {{ $id }}, 'bg-white text-primary-dark': active_menu !== {{ $id }} }">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <span>{{ $title }}</span>
                </div>
                <div>
                    <i class="fal fa-angle-right  group-hover:text-white fa-lg"></i>
                </div>
            </div>

            <ul class="bg-white rounded-md p-1 mt-2"
            x-show="active_menu === {{ $id }}"
            x-transition:enter="transition ease-in-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
            x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
            x-transition:leave="transition ease-in-out duration-300"
            x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">
                @foreach($level2 as $link)
                    <li class="">
                        <a href="{{ $link['link'] }}" class="w-full flex items-center p-2 text-sm text-primary-dark rounded-md hover:text-primary hover:bg-gray-100">
                            <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

        </div>

    </li>

@endif


@if($level === '3')

    <li>

        <div class="relative px-2 py-1 text-sm font-medium cursor-pointer rounded-md group text-primary-dark hover:text-white hover:bg-primary focus:bg-primary"
        :class="{ 'bg-primary text-white': active_menu === {{ $id }}, 'bg-none text-primary-dark': active_menu !== {{ $id }} }">

            <div class="h-full w-full flex justify-between items-center rounded-md pr-3"
            @click="if(active_menu === {{ $id }}) { active_menu = '' } else { active_menu = {{ $id }} }; active_sub_menu = '';">

                <div class="flex justify-start items-center">
                    <div class="flex justify-center items-center h-6 w-6 mr-4 rounded group-hover:bg-primary-light group-hover:text-white"
                    :class="{ 'bg-primary-light text-white': active_menu === {{ $id }}, 'bg-white text-primary-dark': active_menu !== {{ $id }} }">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <span>{{ $title }}</span>
                </div>
                <div>
                    <i class="fal fa-angle-right fa-lg"></i>
                </div>

            </div>


            <ul class="bg-white rounded-md p-1 mt-2"
            x-show="active_menu === {{ $id }}"
            x-transition:enter="transition ease-in-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
            x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
            x-transition:leave="transition ease-in-out duration-300"
            x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">

                @foreach($level3 as $link)

                    @if(!isset($link['sub_links']))

                        <li class="">
                            <a href="{{ $link['link'] }}" class="w-full flex items-center p-2 text-sm text-primary-dark rounded-md hover:text-primary hover:bg-gray-100">
                                <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                            </a>
                        </li>

                    @else

                        <li class="text-sm text-primary-dark rounded-md hover:text-primary hover:bg-gray-100"
                        :class="{ 'bg-gray-100 text-primary': active_sub_menu === {{ $id }} }"
                        @click="if(active_sub_menu === {{ $id }}) { active_sub_menu = '' } else { active_sub_menu = {{ $id }} }">
                            <div class="flex justify-between items-center w-full">
                                <a href="javascript:void(0)" class="w-full flex items-center p-2 ">
                                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                                </a>
                                <div class="mx-3">
                                    <i class="fal fa-angle-right text-primary-dark fa-lg"></i>
                                </div>
                            </div>

                            <ul class="bg-gray-100 p-2 pt-0 ml-2 rounded-md"
                            x-show="active_sub_menu === {{ $id }}"
                            x-transition:enter="transition ease-in-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
                            x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
                            x-transition:leave="transition ease-in-out duration-300"
                            x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
                            x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">
                                @foreach($link['sub_links'] as $link)
                                    <li class="">
                                        <a href="{{ $link['link'] }}" class="text-sm p-2 rounded-md block text-primary-dark hover:text-primary hover:bg-white">
                                            {{ $link['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                    @endif

                @endforeach

            </ul>

        </div>

    </li>


@endif
