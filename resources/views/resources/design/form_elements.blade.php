<x-app-layout>

    <div class="bg-gray-50">

        @section('title') Form Elements @endsection

        <x-slot name="header">
            Form Elements
        </x-slot>

        <div class="pb-36 pt-2 px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Begin Left Side --}}
                <div>

                    {{-- Input --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Input</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.input
                                id="my_input"
                                name="my_input"
                                placeholder="My Input"
                                data-label="My Input"
                                :size="'sm|md|lg|xl'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <x-elements.input
                                id="{{ $size }}_input"
                                name=""
                                placeholder="{{ $size }} Input"
                                data-label="{{ $size }}"
                                :size="$size"/>
                            </div>

                        @endforeach

                    </div>


                    {{-- File Input --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">File Input</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.input-file
                                id="my_input"
                                name="my_input"
                                accept="application/pdf"
                                :size="'sm|md|lg|xl'"
                                :buttonClass="'default|primary|success|danger'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <x-elements.input-file
                                id="{{ $size }}_input_file"
                                accept="application/pdf"
                                :size="$size"
                                :buttonClass="'primary'"/>
                            </div>

                        @endforeach

                    </div>

                    {{-- Dropdown --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Dropdown</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-dropdown
                                :buttonClass="'default|primary|success|danger'"
                                :buttonText="'Open Dropdown'"
                                :buttonSize="'sm|md|lg|xl'"
                                :dropdownClasses="'p-4 bg-white'"
                                :dropdownWidth="'w-96'"
                                :align="'left|right'"&gt
                                This is the dropdown data
                            &lt;/x-dropdown&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">
                        <x-dropdown
                        :buttonClass="'primary'"
                        :buttonText="'Open Dropdown'"
                        :buttonSize="'md'"
                        :dropdownClasses="'p-4 bg-white'"
                        :dropdownWidth="'w-96'"
                        :align="'left'">
                            This is the dropdown data
                        </x-dropdown>
                    </div>

                    {{-- Buttons--}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Buttons</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.button
                                class=""
                                :buttonClass="'default|primary|success|danger'"
                                :buttonSize="'sm|md|lg|xl'"
                                type="button"&gt;
                                &lt;i class="fal fa-check mr-2"&gt;&lt;/i&gt; Save
                            &lt;/x-elements.button&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">
                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                        <div class="mb-3">

                            @foreach(['default', 'primary', 'success', 'danger'] as $class)

                                <x-elements.button
                                    class="mr-2"
                                    :buttonClass="$class"
                                    :buttonSize="$size"
                                    type="button">
                                    <i class="fal fa-check mr-2"></i> {{ ucwords($size.' - '.$class) }}
                                </x-elements.button>

                            @endforeach

                        </div>

                        @endforeach
                    </div>


                    {{-- Slider --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Slider</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200  whitespace-pre-line">
                            &lt;x-elements.range-slider
                                id="my_slider"
                                name="my_slider"
                                value="50"
                                min="0"
                                max="100"
                                step="1"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        <x-elements.range-slider :id="'my_slider'" :name="'my_slider'" :value="'50'" :min="'0'" :max="'100'" :step="'1'"/>

                    </div>


                    {{-- Checkbox --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Check Box</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.check-box
                                id="my_checkbox"
                                name="my_checkbox"
                                checked="checked"
                                value="abc"
                                :size="'sm|md|lg|xl'"
                                :color="'red|blue|etc'"
                                :label="'Check Option'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            @foreach(['red', 'green', 'blue'] as $color)

                                <x-elements.check-box
                                id=""
                                name="my_checkbox"
                                checked="checked"
                                value="abc"
                                :size="$size"
                                :color="$color"
                                :label="'Check Option'"/>

                            @endforeach
                            <br>

                        @endforeach


                    </div>

                    {{-- Radios --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Radios</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200  whitespace-pre-line">
                            &lt;x-elements.radio
                                id="my_radio"
                                name="my_radio"
                                checked="checked"
                                value="abc"
                                :size="'sm|md|lg|xl'"
                                :color="'red|green|etc'"
                                :label="'Radio Option'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            @foreach(['red', 'green', 'blue'] as $color)

                                <x-elements.radio
                                id=""
                                name="{{ $size }}"
                                value="abc"
                                :size="$size"
                                :color="$color"
                                :label="'Radio Option'"/>

                            @endforeach
                            <br>

                        @endforeach

                    </div>



                </div>


                {{-- Begin Right Side --}}
                <div>

                    {{-- Select --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Select</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.select
                            id="my_select"
                            name=""
                            data-label="My Select"
                            :size="'sm|md|lg|xl'"&gt;
                                &lt;option value="">Select&lt;/option&gt;
                                &lt;option value="First"&gt;First&lt;/option&gt;
                                &lt;option value="Second"&gt;Second&lt;/option&gt;
                                &lt;option value="Third"&gt;Third&lt;/option&gt;
                                &lt;option value="Fourth"&gt;Fourth&lt;/option&gt;
                            &lt;/x-elements.select&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <x-elements.select
                                id="{{ $size }}_select"
                                name=""
                                data-label="{{ $size }}"
                                :size="$size">
                                    <option value="">Select</option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                    <option value="Third">Third</option>
                                    <option value="Fourth">Fourth</option>
                                </x-elements.select>
                            </div>

                        @endforeach

                    </div>

                    {{-- Select Multiple --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Select Multiple</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.select-multiple
                            id="my_select_multiple"
                            name=""
                            data-label="My Select Multiple"
                            :size="'sm|md|lg|xl'"&gt;
                                &lt;option class="text-red-500" value="">Select&lt;/Multiple Options&gt;
                                &lt;option value="First"&gt;First&lt;/option&gt;
                                &lt;option value="Second"&gt;Second&lt;/option&gt;
                                &lt;option value="Third"&gt;Third&lt;/option&gt;
                                &lt;option value="Fourth"&gt;Fourth&lt;/option&gt;
                            &lt;/x-elements.select-multiple&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <x-elements.select-multiple
                                id="{{ $size }}_select_multiple"
                                name=""
                                data-label="{{ $size }}"
                                :size="$size">
                                    <option  class="text-red-500">------ Select Multiple</option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                    <option value="Third">Third</option>
                                    <option value="Fourth">Fourth</option>
                                </x-elements.select-multiple>
                            </div>

                        @endforeach

                    </div>


                    {{-- Modals --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Modal</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">

                                &lt;x-elements.button
                                class="mb-3"
                                :buttonClass="'primary'"
                                :buttonSize="'sm'"
                                type="button"
                                @click="show_add_contact_modal = true"&gt;
                                &lt;i class="fad fa-user-friends mr-2"&gt;&lt;/i&gt; Import from Contacts
                                &lt;/x-elements.button&gt;

                                &lt;x-modals.modal
                                :modalWidth="'w-1/2'"
                                :modalTitle="'Import Contact'"
                                :modalId="'show_add_contact_modal'"
                                x-show="show_add_contact_modal"&gt;

                                Stuff goes here

                                &lt;/x-modals.modal&gt;

                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white"
                    x-data="{ show_modal: false }">

                        <x-elements.button
                        class="mb-3"
                        :buttonClass="'primary'"
                        :buttonSize="'md'"
                        type="button"
                        @click="show_modal = true">
                        Show Modal
                        </x-elements.button>

                        <x-modals.modal
                        :modalWidth="'w-1/2'"
                        :modalTitle="'Import Contact'"
                        :modalId="'show_modal'"
                        x-show="show_modal">

                        </x-modals.modal>

                    </div>


                    {{-- Textarea --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Textarea</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.textarea
                                id="my_textarea"
                                name="my_textarea"
                                placeholder="My Textarea"
                                data-label="My Textarea"
                                :size="'sm|md|lg|xl'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <x-elements.textarea
                                id="{{ $size }}_textarea"
                                name=""
                                rows="2"
                                placeholder="{{ $size }} Textarea"
                                data-label="{{ $size }}"
                                :size="$size">Textarea value</x-elements.textarea>
                            </div>

                        @endforeach

                    </div>


                    {{-- Toggle --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Toggle Switch</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                            &lt;x-elements.toggle
                            id="toggle_1"
                            :size="'sm'"
                            :label="'Toggle Label'"/&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @php $c = 0; @endphp
                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)
                            @php $c += 1; @endphp
                            <div class="my-4">
                                <x-elements.toggle
                                id="toggle_{{ $c }}"
                                :size="$size"
                                :label="'Toggle Label'"/>
                            </div>

                        @endforeach

                    </div>




                </div>

            </div>

        </div>

    </div>

</x-app-layout>
