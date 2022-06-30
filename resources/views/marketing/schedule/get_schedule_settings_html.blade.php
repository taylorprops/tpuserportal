@foreach ($categories as $category)
    <div class="border rounded-md p-0" x-data="{ show_add_item: false }">

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
                    <button type="button" class="button primary md" @click="settings_save_add_item($el, '{{ $category }}', $refs.add_{{ $category }}_input)">Save <i
                            class="fa-light fa-check ml-2"></i></button>
                </div>
            </div>
        </div>

        <div class="settings-options" data-category="{{ $category }}">

            @foreach ($settings -> where('category', $category) as $setting)
                <div class="border-b settings-item px-2" data-id="{{ $setting -> id }}" x-data="{ show_color_picker: false, show_companies: false }">

                    <div class="flex justify-between items-center">

                        <div class="flex justify-start items-center">

                            <div class="w-8">
                                <button type="button" class="block setting-handle w-full text-center text-gray-500"><i class="fa-light fa-bars"></i></button>
                            </div>

                            <div class="w-6" x-data="{ locked: {{ $setting -> locked }} }">
                                <a href="javascript:void(0)" class="text-red-700/75" x-show="locked === 1"
                                    @if (auth() -> user() -> level == 'super_admin') @click="if(confirm('Are you sure you want change this?')) { settings_save_edit_item({{ $setting -> id }}, 0, 'locked'); locked = 0; }" @endif><i
                                        class="fa-duotone fa-lock"></i></a>
                                <a href="javascript:void(0)" class="text-green-700/75" x-show="locked === 0"
                                    @if (auth() -> user() -> level == 'super_admin') @click="if(confirm('Are you sure you want change this?')) { settings_save_edit_item({{ $setting -> id }}, 1, 'locked'); locked = 1; }" @endif><i
                                        class="fa-duotone fa-lock-open"></i></a>
                            </div>

                            @if ($setting -> has_color == true)
                                <div class="relative px-2">
                                    <div class="w-8 h-8 border-4 rounded-md bg-{{ $setting -> color }}-500" @click="show_color_picker = ! show_color_picker"></div>
                                    <div class="absolute left-10 top-0 w-48 bg-white z-40 border-4 rounded-md p-4" x-show="show_color_picker" x-transition
                                        @click.outside="show_color_picker = false">

                                        {{-- blade-formatter-disable --}}
                                        @php
                                            $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'rose'];
                                        @endphp
                                        {{-- blade-formatter-enable --}}
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach ($colors as $color)
                                                <div class="bg-{{ $color }}-500 border-2 rounded-md cursor-pointer hover:bg-{{ $color }}-400 h-8 w-8"
                                                    @click="settings_save_edit_item({{ $setting -> id }}, '{{ $color }}', 'color'); show_color_picker = false;"></div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            @endif

                            <div class="px-2">{{ $setting -> id }}</div>

                            <div class="flex justify-between items-center pr-2">
                                <div class="">
                                    <input type="text" class="editor-inline p-2 @if ($setting -> has_email == true) w-28 @endif" value="{{ $setting -> item }}"
                                        data-default-value="{{ $setting -> item }}" @blur="settings_save_edit_item({{ $setting -> id }}, $el.value, 'item')">
                                </div>
                                @if ($setting -> has_email == true)
                                    <div class=" ml-2">
                                        <input type="text" class="editor-inline p-2 w-48" value="{{ $setting -> email }}" data-default-value="{{ $setting -> email }}"
                                            @blur="settings_save_edit_item({{ $setting -> id }}, $el.value, 'email')">
                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="flex justify-end items-center">

                            @if ($setting -> category == 'recipient')
                                {{-- blade-formatter-disable --}}
                                @php
                                    $value = '';
                                    if($setting -> company_ids != '' ){

                                        $companies = explode(',', $setting -> company_ids);

                                        $value = collect($companies) -> map(function($comp) {
                                            if($comp == '1') {
                                                $initials = 'TP';
                                            } else if($comp == '2') {
                                                $initials = 'HF';
                                            } else if($comp == '3') {
                                                $initials = 'HT';
                                            }
                                            return $initials;
                                        });
                                        $value = $value -> implode(', ');
                                    }

                                @endphp
                                {{-- blade-formatter-enable --}}
                                <div class="ml-2">
                                    <button type="button" class="button primary sm" @click="show_companies = !show_companies">{{ $value }}</button>
                                </div>
                            @endif

                            <div class="px-2 w-10">
                                @if ($setting -> locked == false)
                                    <button type="button" class="button danger md no-text"
                                        @click="reassign_disabled = true; settings_show_delete_item('{{ $setting -> category }}', {{ $setting -> id }})">
                                        <i class="fa-duotone fa-xmark fa-xl"></i>
                                    </button>
                                @endif
                            </div>

                        </div>

                    </div>

                    @if ($setting -> category == 'recipient')
                        <div x-show="show_companies">
                            <select class="form-element select md" name="companies[]" multiple x-ref="select" data-label="Select Companies">
                                @foreach ($settings -> where('category', 'company') as $company)
                                    <option value="{{ $company -> id }}" @if (in_array($company -> id, $companies)) selected @endif>{{ $company -> item }}</option>
                                @endforeach
                            </select>
                            <div class="flex justify-around p-2">
                                <button type="button" class="button primary md"
                                    @click="settings_save_edit_item({{ $setting -> id }}, '', 'company_ids', $refs.select); show_companies = false">
                                    Save
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>
@endforeach
