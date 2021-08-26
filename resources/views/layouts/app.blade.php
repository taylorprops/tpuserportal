<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">

        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/fh-3.1.9/kt-2.6.2/r-2.2.9/sp-1.3.0/datatables.min.css"/>



        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/fh-3.1.9/kt-2.6.2/r-2.2.9/sp-1.3.0/datatables.min.js"></script>

        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/vendor/draggable.js') }}"></script> {{-- https://anseki.github.io/plain-draggable/ --}}

        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('global.google_api_key') }}&libraries=places&outputFormat=json"></script>

        <script defer src="https://unpkg.com/alpinejs@3.1.1/dist/cdn.min.js"></script>


    </head>

    <body class="font-sans antialiased">

        <div x-data="nav()" {{-- x-on:resize.window="main_nav_open = (window.outerWidth >= 1280) ? true : false;" @keydown.window.escape="main_nav_open = false" --}} class="min-h-screen flex overflow-hidden">

            @include('layouts.menu.menu')

            <div class="flex flex-col w-0 flex-1 p-1 overflow-hidden">

                <!-- Page Heading -->
                @if($header != 'null')
                <header>
                    <div class="w-full py-1 border-gray-500 border-b"
                    :class="{
                        'ml-72 pl-0': main_nav_open && (window.outerWidth >= 1280),
                        'pl-8 ml-0': !(window.outerWidth >= 1280) || !main_nav_open,
                        'relative': (window.outerWidth >= 1280),
                        'fixed': (window.outerWidth < 1280)
                    }">
                        <h2 class="sm:text-xl md:text-2xl text-gray-600 tracking-wider"
                        :class="{
                            'ml-4' : !main_nav_open,
                            'ml-0' : main_nav_open,
                        }">
                            {{ $header ?? null }}
                        </h2>
                    </div>
                </header>
                @endif

                <!-- Page Content -->
                <main class="p-4" :class="{
                    'ml-72' : main_nav_open && (window.outerWidth >= 1280),
                    '' : !(window.outerWidth >= 1280) || !main_nav_open,
                    'pt-8' : (window.outerWidth <= 640),
                    'pt-12' : (window.outerWidth > 640) && (window.outerWidth < 1280),
                }">
                    {{ $slot }}
                </main>

            </div>

        </div>

        <input type="hidden" id="global_company_active_states" value="{{ implode(',', config('global.company_active_states')) }}">

        <div class="page-loading w-full h-full fixed top-0 left-0 bg-white opacity-70 z-90 hidden">
            <span class="text-gray-700 opacity-75 top-1/3 my-0 mx-auto block relative w-0 h-0">
                <i class="fas fa-circle-notch fa-spin fa-4x"></i>
            </span>
        </div>

    </body>

</html>
