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

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/vendor/draggable.js') }}"></script> {{-- https://anseki.github.io/plain-draggable/ --}}


    </head>

    <body class="font-sans antialiased" x-data="{ show_loading: false }">

        <div class="page-loading w-full h-full fixed block top-0 left-0 bg-white opacity-75 z-50" x-show="show_loading">
            <span class="text-gray-700 opacity-75 top-1/3 my-0 mx-auto block relative w-0 h-0">
                <i class="fas fa-circle-notch fa-spin fa-4x"></i>
            </span>
        </div>

        <div x-data="{ main_nav_open: $screen('xl') }" x-on:resize.window="main_nav_open = (window.outerWidth >= 1280) ? true : false;" @keydown.window.escape="main_nav_open = false" class="min-h-screen flex overflow-hidden bg-gray-100">

            {{-- @include('layouts.navigation') --}}
            @include('layouts.menu.menu')

            <div class="flex flex-col w-0 flex-1 overflow-hidden">

                <div class="pl-1 pt-1 sm:pl-3 sm:pt-3" x-show="!main_nav_open">

                    <button class="fixed -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="main_nav_open = true">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" x-description="Heroicon name: outline/menu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                </div>

                <!-- Page Heading -->
                @if($header != 'null')
                <header class="" :class="{ 'ml-64' : main_nav_open, '' : !main_nav_open }">
                    <div class="max-w-full pb-0 md:pb-5 px-4 sm:px-6 lg:px-8"
                    :class="{ 'mx-auto mt-6' : main_nav_open, 'ml-10 mt-2' : !main_nav_open }">
                        {{ $header ?? null }}
                    </div>
                </header>
                @endif

                <!-- Page Content -->
                <main :class="{ 'ml-64' : main_nav_open, '' : !main_nav_open }">
                    {{ $slot }}
                </main>

            </div>

        </div>

    </body>

</html>
