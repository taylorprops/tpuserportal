<div>

    <div class="sm:hidden">

        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" name="tabs"
            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
            @change="active_tab = $el.value">
            @foreach ($settings -> where('category', 'company') as $setting)
                <option value="{{ $loop -> index }}">{{ $setting -> item }}</option>
            @endforeach
        </select>

    </div>

    <div class="hidden sm:block">

        <div class="border-b border-gray-200">

            <nav class="-mb-px flex space-x-12" aria-label="Tabs">

                @foreach ($settings -> where('category', 'company') as $setting)
                    <a href="javascript:void(0)"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        :class="{
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !==
                                '{{ $loop -> index }}',
                            'border-primary text-primary-dark': active_tab ===
                                '{{ $loop -> index }}'
                        }"
                        @click="active_tab = '{{ $loop -> index }}'">{{ $setting -> item }} </a>
                @endforeach

            </nav>

        </div>

    </div>


    @foreach ($settings -> where('category', 'company') as $setting)
        <div x-show="active_tab === '{{ $loop -> index }}'" x-transition"
            class="p-4 border"
            x-data="{ active_sub_tab: '0' }">

            <div>

                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Select a tab</label>
                    <select id="tabs" name="tabs"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                        @change="active_sub_tab = $el.value">
                        @foreach ($settings -> where('category', 'recipient') as $setting)
                            <option value="{{ $loop -> index }}" selected>{{ $setting -> item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="hidden sm:block">

                    <div class="border-b border-gray-200">

                        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">

                            @foreach ($settings -> where('category', 'recipient') as $setting)
                                <a href="javascript:void(0)"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                    :class="{
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !==
                                            '{{ $loop -> index }}',
                                        'border-primary text-primary-dark': active_sub_tab ===
                                            '{{ $loop -> index }}'
                                    }"
                                    @click="active_sub_tab = '{{ $loop -> index }}'">{{ $setting -> item }}</a>
                            @endforeach

                        </nav>

                    </div>

                </div>


                @foreach ($settings -> where('category', 'recipient') as $setting)
                    <div x-show="active_sub_tab === '{{ $loop -> index }}'" x-transition"
                        class="p-4">

                        <a href="javascript:void(0)" class="button primary md"
                            @click="add_item('{{ $loop -> parent -> index }}', '{{ $loop -> index }}')">Add
                            Item <i
                                class="fa-light fa-plus ml-2"></i>
                        </a>

                        <div x-show="active_sub_tab === '{{ $loop -> index }}'" x-transition"
                            class="p-4">
                            {{ $loop -> parent -> index }} {{ $loop -> index }}
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    @endforeach

</div>
