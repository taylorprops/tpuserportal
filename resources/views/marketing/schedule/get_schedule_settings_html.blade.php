@foreach($categories as $category)

    <div class="border rounded-md p-0"
    x-data="{ show_add_item: false }">

        <div class="bg-gray-50 text-lg p-4 rounded-t-md border-b-2">
            <div class="flex justify-between items-center">
                <div>
                    {{ ucwords($category) }}
                </div>
                <div>
                    <button type="button" class="button primary md" @click="show_add_item = true;">Add <i class="fa-light fa-plus ml-2"></i></button>
                </div>
            </div>
            <div class="flex justify-start p-4 mt-3" x-show="show_add_item" x-transition x-trap="show_add_item">
                <div>
                    <input type="text" class="form-element input md" x-ref="add_{{ $category }}_input">
                </div>
                <div class="ml-2">
                    <button type="button" class="button primary md" @click="settings_save_add_item($el, '{{ $category }}', $refs.add_{{ $category }}_input)">Save <i class="fa-light fa-check ml-2"></i></button>
                </div>
            </div>
        </div>

        <div>
            @foreach($settings -> where('category', $category) as $setting)
                <div class="flex justify-start items-center my-2 border-b"
                x-data="{ show_color_picker: false }">
                    {{-- <div class="pl-2">
                        <input type="color" class="w-8 h-8 border-4 rounded" value="{{ $setting -> color }}"
                        @change="settings_save_edit_item({{ $setting -> id }}, $el.value, 'color')">
                    </div> --}}
                    <div class="pl-2 relative">
                        <div class="w-8 h-8 border-4 rounded-md bg-{{ $setting -> color }}-600"
                            @click="show_color_picker = ! show_color_picker"></div>
                        <div class="absolute left-10 top-0 w-48 border-4 rounded-md p-4"
                        x-show="show_color_picker" x-transition>

                        </div>
                    </div>
                    <div class="flex justify-between items-center pr-4">
                        <div class="w-52">
                            <input type="text" class="editor-inline p-2" value="{{ $setting -> item }}"
                            @blur="settings_save_edit_item({{ $setting -> id }}, $el.value, 'item')">
                        </div>
                        <div>
                            <button type="button" class="button danger md no-text"
                            @click="settings_show_delete_item(category, {{ $setting -> id }})">
                                <i class="fa-duotone fa-xmark fa-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

@endforeach
