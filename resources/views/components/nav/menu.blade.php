

@if($level == '1')
    <li>
        <a href="{{ $link }}" class="flex items-center text-gray-300 hover:bg-default-light hover:text-white px-2 py-2 text-sm font-medium focus:bg-default-light">
            <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                <i class="{{ $icon }}"></i>
            </div>
            {{ $title }}
        </a>
    </li>
@endif


@if($level == '2')

    <li class="one relative w-full cursor-pointer text-gray-300 hover:bg-default-light hover:text-white px-2 py-2 text-sm font-medium focus:bg-default-light">

        <div class="flex justify-between items-center w-full">

            <div class="flex justify-start items-center">
                <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                    <i class="{{ $icon }}"></i>
                </div>
                {{ $title }}
            </div>
            <div>
                <i class="fal fa-angle-right text-gray-300 fa-lg"></i>
            </div>
        </div>

        <ul class="bg-default-light shadow absolute left-3/4 top-0 min-w-max p-2 z-10">
            @foreach($level2 as $link)
                <li class="text-white hover:text-primary p-2 pl-4">
                    <a href="{{ $link['link'] }}" class="w-full flex items-center text-sm font-medium rounded-md text-white hover:text-gray-300">
                        <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>

    </li>


@endif


@if($level == '3')

    <li class="one relative w-full cursor-pointer text-gray-300 hover:bg-default-light hover:text-white px-2 py-2 text-sm font-medium focus:bg-default-light">

        <div class="flex justify-between items-center w-full">

            <div class="flex justify-start items-center">
                <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                    <i class="{{ $icon }}"></i>
                </div>
                {{ $title }}
            </div>
            <div>
                <i class="fal fa-angle-right text-gray-300 fa-lg"></i>
            </div>
        </div>

        <ul class="bg-default-light shadow absolute left-3/4 top-0 min-w-max p-2 z-10">

            @foreach($level3 as $link)

                @if(!isset($link['sub_links']))

                    <li class="text-white hover:text-primary p-2 pl-4">
                        <a href="{{ $link['link'] }}" class="w-full flex items-center text-sm font-medium rounded-md text-white hover:text-gray-300">
                            <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                        </a>
                    </li>

                @else

                    <li class="two relative text-white hover:text-primary p-2 pl-4">
                        <div class="flex justify-between items-center w-full">
                            <a href="javascript:void(0)" class="w-full flex items-center text-sm font-medium rounded-md text-white hover:text-gray-300">
                                <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                            </a>
                            <div class="ml-3">
                                <i class="fal fa-angle-right text-gray-300 fa-lg"></i>
                            </div>
                        </div>

                        <ul class="bg-default-light shadow absolute left-3/4 top-0 w-48 p-2">
                            @foreach($link['sub_links'] as $link)
                                <li class="p-2 pl-4">
                                    <a href="{{ $link['link'] }}" class="w-full flex items-center text-sm font-medium rounded-md text-white hover:text-gray-300">
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





   {{--  <div class="nav-link w-full">

        <div class="cursor-pointer text-gray-300 hover:bg-default-light hover:text-white flex items-center px-2 py-2 text-sm font-medium focus:bg-default-light">

            <div class="flex justify-between items-center w-full">

                <div class="flex justify-start items-center">
                    <div class="bg-default-light h-7 w-7 rounded mr-2 flex justify-center items-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                    {{ $title }}
                </div>
                <div>
                    <i class="fal fa-angle-right text-gray-300 fa-lg"></i>
                </div>
            </div>

        </div>

        <div class="space-y-1 bg-default-light" x-transition>

            @foreach($level3 as $link)

                @if(!isset($link['sub_links']))

                    <a href="{{ $link['link'] }}" class="w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                        <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                    </a>

                @else

                    <div class="nav-link w-full">

                        <div class="cursor-pointer w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">

                            <div class="flex justify-between items-center w-full">

                                <div class="flex justify-start items-center">
                                    <i class="{{ $link['icon'] }}"></i> {{ $link['title'] }}
                                </div>
                                <div>
                                    <i class="fal fa-angle-right text-gray-300"></i>
                                </div>
                            </div>

                        </div>


                        <!-- Expandable link section, show/hide based on state. -->
                        <div class="space-y-1 bg-default-light rounded pl-8" x-transition>
                            @foreach($link['sub_links'] as $link)
                                <a href="{{ $link['link'] }}" class="w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                                    {{ $link['title'] }}
                                </a>
                            @endforeach

                        </div>

                    </div>

                @endif

            @endforeach

        </div>


    </div> --}}

@endif
