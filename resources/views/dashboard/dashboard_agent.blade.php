<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    <x-elements.input class="shadow-sm border border-gray-300 focus:ring-indigo-500 focus:border-blue-200 block max-w-md sm:text-sm border-gray-300 rounded-md p-4 placeholder-gray-400" placeholder="Etner Text"/>

                    <br><br>

                    <select class="shadow-sm border border-gray-300 focus:ring-indigo-500 focus:border-blue-200 block max-w-md sm:text-sm border-gray-300 rounded-md p-4 pr-8">
                        <option value="">Make Choice</option>
                        <option value="a">First</option>
                        <option value="b">Second</option>
                        <option value="ea">Third</option>
                        <option value="asda">Last</option>
                    </select>

                    <br><br>

                    <select class="shadow-sm border border-gray-300 focus:ring-indigo-500 focus:border-blue-200 block max-w-md sm:text-sm border-gray-300 rounded-md p-4" multiple>
                        <option value="" class="text-red-500" disabled>---------</option>
                        <option value="a">First</option>
                        <option value="b">Second</option>
                        <option value="ea">Third</option>
                        <option value="asda">Last</option>
                    </select>

                    <br>
                    <br>
                    @php
                        $buttonText = 'Click Me';
                        $buttonClasses = config('elements.btn-primary-classes');
                    @endphp
                    <x-dropdown :buttonClasses="$buttonClasses" :buttonText="$buttonText">
                        This is thte eonasdif j;asdlfj a;lsdj f;lasdf
                    </x-dropdown>

                    <br><br>





                    <div>

                        @php $modalId = 'firstone'; $dispatchId = time(); @endphp

                        <button type="button" id="open_modal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" x-data @click="$dispatch('show-modal-{{ $dispatchId }}')">
                            Open Modal
                        </button>

                        <x-modals.modal :modalId="$modalId" :dispatchId="$dispatchId">

                            <div class="sm:flex sm:items-start">

                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <!-- Heroicon name: outline/exclamation -->
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>

                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Deactivate account
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to deactivate your account? All of your data will be permanently removed from our servers forever. This action cannot be undone.
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">

                                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" @click="show_modal = false">
                                    Deactivate
                                </button>
                                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"  @click="show_modal = false">
                                    Cancel
                                </button>

                            </div>

                        </x-modals.modal>

                        @php $modalId = 'secondone'; $dispatchId = time() + 1; @endphp

                        <button type="button" id="open_modal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" x-data @click="$dispatch('show-modal-{{ $dispatchId }}')">
                            Open Modal 2
                        </button>

                        <x-modals.modal :modalId="$modalId" :dispatchId="$dispatchId">

                            <div class="sm:flex sm:items-start">

                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">

                                </div>

                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Eat account
                                    </h3>

                                </div>

                            </div>

                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">

                                word yo

                            </div>

                        </x-modals.modal>

                    </div>


                </div>

                <div class="h-screen"></div>

            </div>

        </div>

    </div>
</x-app-layout>
