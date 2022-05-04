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

        <div class="">
            @foreach($settings -> where('category', $category) as $setting)
                <div class="flex justify-start items-center my-2 border-b"
                x-data="{ show_color_picker: false }">
                    @if($setting -> has_color == true)
                        <div class="pl-2 relative">
                            <div class="w-8 h-8 border-4 rounded-md bg-{{ $setting -> color }}-500"
                                @click="show_color_picker = ! show_color_picker"></div>
                            <div class="absolute left-10 top-0 w-48 bg-white z-40 border-4 rounded-md p-4"
                            x-show="show_color_picker" x-transition
                            @click.outside="show_color_picker = false">

                                @php
                                $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'rose'];
                                @endphp
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($colors as $color)

                                        <div class="bg-{{ $color }}-500 border-2 rounded-md cursor-pointer hover:bg-{{ $color }}-400 h-8 w-8"
                                        @click="settings_save_edit_item({{ $setting -> id }}, '{{ $color }}', 'color'); show_color_picker = false;"></div>

                                    @endforeach
                                </div>

                            </div>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pr-2 w-full">
                        <div class="">
                            <input type="text" class="editor-inline p-2" value="{{ $setting -> item }}"
                            @blur="settings_save_edit_item({{ $setting -> id }}, $el.value, 'item')">
                        </div>
                        <div class="">
                            <button type="button" class="button danger md no-text"
                            @click="reassign_disabled = true; settings_show_delete_item('{{ $setting -> category }}', {{ $setting -> id }})">
                                <i class="fa-duotone fa-xmark fa-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

@endforeach
