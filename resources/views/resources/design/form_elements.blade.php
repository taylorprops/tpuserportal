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
                                Options: <span class="text-red-800">sm, md, lg, xl</span><br>
                                &lt;input type="text" class="form-element input <span class="text-red-800">md</span>" placeholder="Size" data-label="Regular Input"&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <input type="text" class="form-element input {{ $size }}" placeholder="{{ $size }}" data-label="Regular Input">
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
                                Options: <span class="text-red-800">sm, md, lg, xl</span><br>
                                &lt;input type="file" class="form-element input <span class="text-red-800">md</span>" placeholder="Size" data-label="File Input"&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <input type="file" class="form-element input {{ $size }}" placeholder="{{ $size }}" data-label="File Input">
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
                                Options: <span class="text-red-800">sm, md, lg | default, primary, secondary, danger, success</span><br>
                                &lt;button type="button" class="button <span class="text-red-800">default md</span>"&gt;
                                &lt;i class="fal fa-check mr-2"&gt;&lt;/i&gt; md default
                                &lt;/button&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">
                        @foreach(['sm', 'md', 'lg'] as $size)

                        <div class="mb-3">

                            @foreach(['default', 'primary', 'success', 'danger'] as $class)
                                <span class="m-3">
                                    <button type="button" class="button {{ $class }} {{ $size }}">
                                        <i class="fal fa-check mr-2"></i> {{ ucwords($size.' - '.$class) }}
                                    </button>
                                </span>
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
                                Options: <span class="text-red-800">sm, md, lg, xl | primary, secondary, danger, success</span><br>
                                &lt;input type="checkbox" class="form-element checkbox <span class="text-red-800">md primary</span>" data-label="Check Option"&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            @foreach(['primary', 'secondary', 'success', 'danger'] as $class)

                                <span class="mr-3">

                                    <input type="checkbox" class="form-element checkbox {{ $size }} {{ $class }}" data-label="Check Option">

                                </span>

                            @endforeach

                            <br><br>

                        @endforeach


                    </div>

                    {{-- Radios --}}
                    <div x-data="{ show: false }">

                        <div class="flex justify-start items-center mt-6 mb-3">
                            <div class="text-lg text-yellow-700 mr-4">Radio</div>
                            <a href="javascript:void(0)" class="text-sm text-gray-500" @click="show = !show">
                                <i class="fal fa-plus mr-2"></i> Show Code
                            </a>
                        </div>

                        <div x-show="show">
                            <pre class="p-4 border mb-2 bg-gray-200 whitespace-pre-line">
                                Options: <span class="text-red-800">sm, md, lg, xl | primary, secondary, danger, success</span><br>
                                &lt;input type="radio" class="form-element radio <span class="text-red-800">md primary</span>" data-label="Radio Option"&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            @foreach(['primary', 'secondary', 'success', 'danger'] as $class)
                                <span class="mr-3">
                                    <input type="radio" class="form-element radio {{ $size }} {{ $class }}" data-label="Radio Option" name="{{ $size }}">
                                </span>
                            @endforeach
                            <br>
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
                                Options: <span class="text-red-800">sm, md, lg, xl</span><br>
                                &lt;select
                                class="form-element select <span class="text-red-800">md</span>"
                                id=""
                                name=""
                                data-label="Select Element"&gt;
                                    &lt;option value=""&gt;Select&lt;/option&gt;
                                    &lt;option value="First"&gt;First&lt;/option&gt;
                                    &lt;option value="Second"&gt;Second&lt;/option&gt;
                                    &lt;option value="Third"&gt;Third&lt;/option&gt;
                                    &lt;option value="Fourth"&gt;Fourth&lt;/option&gt;
                                &lt;/select&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <select
                                class="form-element select {{ $size }}"
                                id=""
                                name=""
                                data-label="{{ $size }}">
                                    <option value="">Select</option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                    <option value="Third">Third</option>
                                    <option value="Fourth">Fourth</option>
                                </select>
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
                                Options: <span class="text-red-800">sm, md, lg, xl</span><br>
                                &lt;select
                                class="form-element select <span class="text-red-800">md</span>"
                                multiple
                                id=""
                                name=""
                                data-label="Select Element"&gt;
                                    &lt;option value=""&gt;Select&lt;/option&gt;
                                    &lt;option value="First"&gt;First&lt;/option&gt;
                                    &lt;option value="Second"&gt;Second&lt;/option&gt;
                                    &lt;option value="Third"&gt;Third&lt;/option&gt;
                                    &lt;option value="Fourth"&gt;Fourth&lt;/option&gt;
                                &lt;/select&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <select
                                multiple
                                class="form-element select {{ $size }}"
                                id=""
                                name=""
                                data-label="{{ $size }}">
                                    <option value="">Select</option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                    <option value="Third">Third</option>
                                    <option value="Fourth">Fourth</option>
                                </select>
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

                                &lt;button
                                type="button"
                                class="button primary md mb-3"
                                @click="show_modal = true">
                                Show Modal <i class="fal fa-plus ml-2"></i>
                                </button&gt;

                                &lt;x-modals.modal
                                :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
                                :modalTitle="'Import Contact'"
                                :modalId="'show_modal'"
                                x-show="show_modal"&gt;

                                Stuff goes here

                                &lt;/x-modals.modal&gt;

                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white"
                    x-data="{ show_modal: false }">

                        <button
                        type="button"
                        class="button primary md mb-3"
                        @click="show_modal = true">
                        Show Modal <i class="fal fa-plus ml-2"></i>
                        </button>

                        <x-modals.modal
                        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
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
                            &lt;textarea
                            class="form-element textarea md"
                            rows="2"&gt
                                Textarea value
                            &lt;/textarea&gt;
                            </pre>
                        </div>
                    </div>

                    <div class="mb-4 rounded p-3 bg-white">

                        @foreach(['sm', 'md', 'lg', 'xl'] as $size)

                            <div class="my-2">
                                <textarea class="form-element textarea {{ $size }}" rows="2">Textarea value</textarea>
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
