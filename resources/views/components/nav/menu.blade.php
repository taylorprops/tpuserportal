
@php
$id = time() * rand();
@endphp

@if($level == '1')
    <li>
        <a href="{{ $link }}" class="block h-full w-full rounded-md flex items-center text-gray-700 hover:bg-gray-300 hover:text-gray-900 px-2 py-2 text-sm font-medium focus:bg-gray-200">
            <div class="bg-gray-50 h-7 w-7 rounded mr-2 flex justify-center items-center">
                <i class="{{ $icon }}"></i>
            </div>
            {{ $title }}
        </a>
    </li>
@endif


@if($level == '2')

    <li class="w-full rounded-md cursor-pointer text-gray-700 hover:bg-gray-300 hover:text-gray-900 px-2 py-2 text-sm font-medium focus:bg-gray-200"
    :class="{ 'bg-gray-300 text-gray-900': active_menu == {{ $id }} }"
    @click="if(active_menu == {{ $id }}) { active_menu = '' } else { active_menu = {{ $id }} }; active_sub_menu = '';">

        <div class="h-full w-full flex justify-between items-center rounded-md">

            <div class="flex justify-start items-center">
                <div class="bg-gray-50 h-7 w-7 rounded mr-2 flex justify-center items-center">
                    <i class="{{ $icon }}"></i>
                </div>
                {{ $title }}
            </div>
            <div>
                <i class="fal fa-angle-right text-gray-700 fa-lg"></i>
            </div>
        </div>

        <ul class="bg-white rounded-md p-3 mt-2"
        x-show="active_menu == {{ $id }}"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
        x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">
            @foreach($level2 as $link)
                <li class="p-2 pl-4">
                    <a href="{{ $link['link'] }}" class="w-full flex items-center text-xs text-gray-700 hover:text-gray-900">
                        <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>

    </li>


@endif


@if($level == '3')

    <li class="relative rounded-md w-full cursor-pointer text-gray-700 hover:bg-gray-300 hover:text-gray-900 px-2 py-2 text-sm font-medium focus:bg-gray-200"
    :class="{ 'bg-gray-300 text-gray-900': active_menu == {{ $id }} }"
    @click="if(active_menu == {{ $id }}) { active_menu = '' } else { active_menu = {{ $id }} }; active_sub_menu = '';">

        <div class="h-full w-full flex justify-between items-center rounded-md">

            <div class="flex justify-start items-center">
                <div class="bg-gray-50 h-7 w-7 rounded mr-2 flex justify-center items-center">
                    <i class="{{ $icon }}"></i>
                </div>
                {{ $title }}
            </div>
            <div>
                <i class="fal fa-angle-right text-gray-700 fa-lg"></i>
            </div>

        </div>

        <ul class="bg-white rounded-md p-3 mt-2"
        x-show="active_menu == {{ $id }}"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
        x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">

            @foreach($level3 as $link)

                @if(!isset($link['sub_links']))

                    <li class="p-2 pl-4">
                        <a href="{{ $link['link'] }}" class="w-full flex items-center text-xs text-gray-700 hover:text-gray-900">
                            <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                        </a>
                    </li>

                @else

                    <li class="p-2 pl-4">
                        <div class="flex justify-between items-center w-full"
                        @click="if(active_sub_menu == {{ $id }}) { active_sub_menu = '' } else { active_sub_menu = {{ $id }} }">
                            <a href="javascript:void(0)" class="w-full flex items-center text-xs text-gray-700 hover:text-gray-900">
                                <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                            </a>
                            <div class="ml-3">
                                <i class="fal fa-angle-right text-gray-700 fa-lg"></i>
                            </div>
                        </div>

                        <ul class="bg-gray-100 p-3 mt-2 rounded-md"
                        x-show="active_sub_menu == {{ $id }}"
                        x-transition:enter="transition ease-in-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-1/2"
                        x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0"
                        x-transition:leave="transition ease-in-out duration-300"
                        x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0"
                        x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-1/2">
                            @foreach($link['sub_links'] as $link)
                                <li class="p-2 pl-4">
                                    <a href="{{ $link['link'] }}" class="text-xs text-gray-700 hover:text-gray-900">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                @endif

            @endforeach

        </ul>

    </li>



@endif
