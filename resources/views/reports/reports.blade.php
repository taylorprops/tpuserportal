@php
$title = 'Reports';
$breadcrumbs = [
[$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2" x-data="reports()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="mt-16">

                <div>

                    <div class="sm:hidden">

                        <label for="tabs" class="sr-only">Select Report Type</label>
                        <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" @change="active_tab = $el.value">
                            <option value="1" selected>Mortgage</option>
                            <option value="2">Real Estate</option>
                            <option value="3">Title</option>
                        </select>

                    </div>

                    <div class="hidden sm:block">

                        <div class="border-b border-gray-200">

                            <nav class="-mb-px flex space-x-16" aria-label="Tabs">

                                <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 1" :class="{ 'border-primary-light text-primary': active_tab === 1, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 1 }">
                                    Mortgage
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 2" :class="{ 'border-primary-light text-primary': active_tab === 2, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 2 }">
                                    Real Estate
                                </a>

                                <a href="#" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg no-color" @click="active_tab = 3" :class="{ 'border-primary-light text-primary': active_tab === 3, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== 3 }">
                                    Title
                                </a>

                            </nav>

                        </div>

                    </div>


                    <div x-show="active_tab === 1" x-transition>

                        <div class="pt-12">

                            <div class="text-xl font-semibold text-gray-700">Mortgage Reports</div>

                            <div class="flex flex-col">

                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">

                                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">

                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Loan Officer
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Borrower
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Address
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Type
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Type
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Type
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            Jane Cooper
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            Regional Paradigm Technician
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            jane.cooper@example.com
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            Admin
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div x-show="active_tab === 2" x-transition> No Data </div>

                    <div x-show="active_tab === 3" x-transition> No Data </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
