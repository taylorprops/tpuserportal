<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://kit.fontawesome.com/ff68a2cde5.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/vendor/draggable.js') }}"></script> {{-- https://anseki.github.io/plain-draggable/ --}}

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('global.google_api_key') }}&libraries=places&outputFormat=json"></script>

    {{-- text editor --}}
    <script src="https://cdn.tiny.cloud/1/t3u7alod16y8nsqt07h4m5kwfw8ob9sxbvy2rlmrqo94zrui/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js" referrerpolicy="origin"></script>

    <script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased overflow-x-hidden"
    x-data="nav();"
    x-on:resize.window="main_nav_open = (window.innerWidth >= 1280) ? true : false;"
    @keydown.window.escape="main_nav_open = false">

    <div class="flex relative"
        x-data="main_search()">

        <div class="absolute top-12 left-0 z-100">
            {{-- <div class="flex justify-end w-full items-center">
                    <div>
                        <button type="button" class="button danger md no-text"
                        @click="$refs.search_results_div.innerHTML = ''; $refs.search_input.value = ''">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div> --}}
            <div class="absolute top-12 left-0 w-screen lg:w-screen-60 bg-gray-200 z-100 shadow max-h-500-px overflow-y-auto p-2 sm:p-6"
                style="display: none;" x-ref="search_results_div"
                x-show="show_search_results_div"
                @click.outside="$el.innerHTML = '';
                show_search_results_div = false;
                document.querySelectorAll('.main-search-input').forEach(function(input) {
                    input.value = '';
                });">
            </div>
        </div>

        @include('layouts.menu.menu')

        <div class="flex flex-col w-0 flex-1 min-h-screen">

            <div class="flex justify-end mr-6 mt-3 sm:hidden">
                <div class="max-w-100-px relative">
                    <input class="main-search-input form-element input sm" type="text" placeholder="Search"
                        @keyup.debounce="search($el)">
                    <i class="fal fa-search absolute top-2 sm:top-2 right-3"></i>
                </div>
            </div>
            <!-- Page Heading -->
            @if ($header != 'null')
                <header>
                    <div class="hidden sm:inline-block relative w-full border-gray-200 border-b bg-white pt-2"
                        :class="{
                            'pl-72 ml-0': main_nav_open && (window.innerWidth >= 1280),
                            'pl-12 ml-0': !(window.innerWidth >= 1280) || !main_nav_open,
                            /* 'relative': (window.innerWidth >= 1280),
                            'fixed': (window.innerWidth < 1280) */
                        }">
                        <h2 class="text-gray-600 tracking-wider"
                            :class="{
                                'ml-4': !main_nav_open,
                                'ml-0': main_nav_open,
                            }">
                            {{ $header ?? null }}
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="overflow-x-auto "
                :class="{
                    'ml-64': main_nav_open && (window.innerWidth >= 1280),
                    '': !(window.innerWidth >= 1280) || !main_nav_open,
                    'pt-8': (window.innerWidth <= 640),
                    'pt-12': (window.innerWidth > 640) && (window.innerWidth < 1280),
                }">
                {{ $slot }}
            </main>

        </div>

    </div>

    <input type="hidden" id="global_taylor_properties_active_states" value="{{ implode(',', config('global.taylor_properties_active_states')) }}">

    <div class="page-loading w-full h-full fixed top-0 left-0 bg-white opacity-70 z-90 hidden">
        <span class="text-gray-700 opacity-75 top-1/3 my-0 mx-auto block relative w-0 h-0">
            <i class="fas fa-circle-notch fa-spin fa-4x"></i>
        </span>
    </div>

</body>

</html>
