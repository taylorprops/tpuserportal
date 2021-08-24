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
    <script defer src="https://unpkg.com/alpinejs@3.1.1/dist/cdn.min.js"></script>

</head>

<body>
    <div class="h-screen flex">
        <div class="flex flex-col w-64">

            <div class="flex flex-col flex-grow border-r border-gray-200 pt-5 pb-4 bg-white overflow-y-auto">

                <div class="flex items-center flex-shrink-0 px-4">
                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-logo-indigo-600-mark-gray-800-text.svg" alt="Workflow">
                </div>

                <div class="mt-5 flex-grow flex flex-col">

                    <nav class="flex-1 px-2 space-y-1 bg-white" aria-label="Sidebar">


                        <div>
                            <a href="#" class="bg-gray-100 text-gray-900 w-full flex items-center pl-2 py-2 text-sm font-medium rounded-md">
                                <i class="fad fa-tachometer-alt fa-lg mr-3"></i>
                                Dashboard
                            </a>
                        </div>


                        <div x-data="{ open: false }" class="space-y-1">
                            <button type="button" class=" hover:bg-gray-50 hover:text-gray-900 group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"aria-controls="sub-menu-1" @click="open = !open" :class="{ 'bg-gray-50': open, 'bg-white': !(open) }">
                                <i class="fad fa-users fa-lg mr-3"></i>
                                <span class="flex-1">
                                    Team
                                </span>

                                <i class="fa fa-caret-right text-gray-300 mr-3 flex-shrink-0  transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" :class="{ 'text-gray-400 rotate-90': open, 'text-gray-300': !(open) }"></i>
                            </button>
                            <div class="space-y-1" id="sub-menu-1" x-show="open" style="display: none;" :class="{ 'bg-gray-50': open, 'bg-white': !(open) }">

                                <div x-data="{ sub_open: false }" class="flex justify-between items-center ml-8 p-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    <a href="#" class="">
                                        Overview
                                    </a>
                                    <i class="fa fa-caret-right text-gray-300 mr-3 flex-shrink-0  transform hover:text-gray-400 transition-colors ease-in-out duration-150" :class="{ 'text-gray-400 rotate-90': sub_open, 'text-gray-300': !(sub_open) }"></i>
                                </div>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Members
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Calendar
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Settings
                                </a>

                            </div>
                        </div>


                        <div x-data="{ open: false }" class="space-y-1">
                            <button type="button" class="bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" x-state:on="Current" x-state:off="Default" aria-controls="sub-menu-2" @click="open = !open" aria-expanded="false" x-bind:aria-expanded="open.toString()" x-state-description="Current: &quot;bg-gray-100 text-gray-900&quot;, Default: &quot;bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900&quot;">
                                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500" x-description="Heroicon name: outline/folder" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                <span class="flex-1">
                                    Projects
                                </span>
                                <svg class="text-gray-300 ml-3 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" viewBox="0 0 20 20" x-state:on="Expanded" x-state:off="Collapsed" aria-hidden="true" :class="{ 'text-gray-400 rotate-90': open, 'text-gray-300': !(open) }">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <div x-description="Expandable link section, show/hide based on state." class="space-y-1" id="sub-menu-2" x-show="open" style="display: none;">

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Overview
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Members
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Calendar
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Settings
                                </a>

                            </div>
                        </div>


                        <div x-data="{ open: false }" class="space-y-1">
                            <button type="button" class="bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" x-state:on="Current" x-state:off="Default" aria-controls="sub-menu-3" @click="open = !open" aria-expanded="true" x-bind:aria-expanded="open.toString()" x-state-description="Current: &quot;bg-gray-100 text-gray-900&quot;, Default: &quot;bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900&quot;">
                                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500" x-description="Heroicon name: outline/calendar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="flex-1">
                                    Calendar
                                </span>
                                <svg class="ml-3 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150 text-gray-400 rotate-90" viewBox="0 0 20 20" x-state:on="Expanded" x-state:off="Collapsed" aria-hidden="true" :class="{ 'text-gray-400 rotate-90': open, 'text-gray-300': !(open) }">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <div x-description="Expandable link section, show/hide based on state." class="space-y-1" id="sub-menu-3" x-show="open">

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Overview
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Members
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Calendar
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    S<script defer src="https://unpkg.com/alpinejs@3.1.1/dist/cdn.min.js"></script>ettings
                                </a>

                            </div>
                        </div>


                        <div x-data="{ open: false }" class="space-y-1">
                            <button type="button" class="bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" x-state:on="Current" x-state:off="Default" aria-controls="sub-menu-4" @click="open = !open" aria-expanded="false" x-bind:aria-expanded="open.toString()" x-state-description="Current: &quot;bg-gray-100 text-gray-900&quot;, Default: &quot;bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900&quot;">
                                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500" x-description="Heroicon name: outline/inbox" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <span class="flex-1">
                                    Documents
                                </span>
                                <svg class="text-gray-300 ml-3 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" viewBox="0 0 20 20" x-state:on="Expanded" x-state:off="Collapsed" aria-hidden="true" :class="{ 'text-gray-400 rotate-90': open, 'text-gray-300': !(open) }">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <div x-description="Expandable link section, show/hide based on state." class="space-y-1" id="sub-menu-4" x-show="open" style="display: none;">

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Overview
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Members
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Calendar
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Settings
                                </a>

                            </div>
                        </div>


                        <div x-data="{ open: false }" class="space-y-1">
                            <button type="button" class="bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 group w-full flex items-center pl-2 pr-1 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" x-state:on="Current" x-state:off="Default" aria-controls="sub-menu-5" @click="open = !open" aria-expanded="false" x-bind:aria-expanded="open.toString()" x-state-description="Current: &quot;bg-gray-100 text-gray-900&quot;, Default: &quot;bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900&quot;">
                                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500" x-description="Heroicon name: outline/chart-bar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="flex-1">
                                    Reports
                                </span>
                                <svg class="text-gray-300 ml-3 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150" viewBox="0 0 20 20" x-state:on="Expanded" x-state:off="Collapsed" aria-hidden="true" :class="{ 'text-gray-400 rotate-90': open, 'text-gray-300': !(open) }">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <div x-description="Expandable link section, show/hide based on state." class="space-y-1" id="sub-menu-5" x-show="open" style="display: none;">

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Overview
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Members
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Calendar
                                </a>

                                <a href="#" class="group w-full flex items-center pl-11 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50">
                                    Settings
                                </a>

                            </div>
                        </div>

                    </nav>
                </div>
            </div>

        </div>

        <div class="p-4 h-full w-full">
            <div class="text-xl font-semibold">Dashboard</div>
        </div>
    </div>
</body>

</html>
