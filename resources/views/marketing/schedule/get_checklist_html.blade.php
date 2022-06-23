<div>

    <div class="sm:hidden">

        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" name="tabs"
            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
            @change="active_tab = $el.value">
            @foreach ($settings -> where('category', 'company') as $company)
                <option value="{{ $loop -> index }}">{{ $company -> item }}</option>
            @endforeach
        </select>

    </div>

    <div class="hidden sm:block">

        <div class="border-b border-gray-200">

            <div class="text-lg font-semibold text-secondary">Company</div>

            <nav class="-mb-px flex items-center space-x-12" aria-label="Tabs">

                @foreach ($settings -> where('category', 'company') as $company)
                    <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-id="company_button_{{ $company -> id }}"
                        :class="{
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '{{ $loop -> index }}',
                            'border-primary text-primary-dark': active_tab === '{{ $loop -> index }}'
                        }"
                        @click="active_tab = '{{ $loop -> index }}'; console.log('company_button clicked')">{{ $company -> item }} </a>
                @endforeach

            </nav>

        </div>

    </div>


    @foreach ($settings -> where('category', 'company') as $company)
        @php
            if ($company -> id == 1) {
                $states = config('global.taylor_properties_active_states');
            } elseif ($company -> id == 2) {
                $states = config('global.heritage_financial_active_states');
            } elseif ($company -> id == 3) {
                $states = config('global.heritage_title_active_states');
            }
            
        @endphp

        <div x-show="active_tab === '{{ $loop -> index }}'" data-id="company_div_{{ $company -> id }}" x-transition" class="p-8 pr-2 border" x-data="{ active_sub_tab: '0' }">

            <div>

                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Select a tab</label>
                    <select id="tabs" name="tabs"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                        @change="active_sub_tab = $el.value">
                        @foreach ($settings -> where('category', 'recipient') as $recipient)
                            <option value="{{ $loop -> index }}">{{ $recipient -> item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="hidden sm:block">

                    <div class="border-b border-gray-200">

                        <div class="text-lg font-semibold text-secondary">Recipient</div>

                        <nav class="-mb-px flex items-center space-x-8 overflow-x-auto" aria-label="Tabs">

                            @foreach ($settings -> where('category', 'recipient') as $recipient)
                                <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                    data-id="recipient_button_{{ $recipient -> id }}"
                                    :class="{
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '{{ $loop -> index }}',
                                        'border-primary text-primary-dark': active_sub_tab === '{{ $loop -> index }}'
                                    }"
                                    @click="active_sub_tab = '{{ $loop -> index }}'; console.log('recipient_button clicked')">{{ $recipient -> item }}</a>
                            @endforeach

                        </nav>

                    </div>

                </div>


                @foreach ($settings -> where('category', 'recipient') as $recipient)
                    <div x-show="active_sub_tab === '{{ $loop -> index }}'" x-transition" class="p-8 pr-2" data-id="recipient_div_{{ $recipient -> id }}">

                        <div x-data="{ active_sub_sub_tab: '0' }">

                            <div class="sm:hidden">
                                <label for="tabs" class="sr-only">Select a tab</label>
                                <select id="tabs" name="tabs"
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                                    @change="active_sub_tab = $el.value">
                                    @foreach ($states as $state)
                                        <option value="{{ $state }}" selected>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="hidden sm:block">

                                <div class="border-b border-gray-200">

                                    <div class="text-lg font-semibold text-secondary">State</div>

                                    <nav class="-mb-px flex items-center space-x-8 overflow-x-auto" aria-label="Tabs">

                                        @foreach ($states as $state)
                                            <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                                data-id="state_{{ $state }}"
                                                :class="{
                                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_sub_tab !== '{{ $loop -> index }}',
                                                    'border-primary text-primary-dark': active_sub_sub_tab === '{{ $loop -> index }}'
                                                }"
                                                @click="active_sub_sub_tab = '{{ $loop -> index }}'">{{ $state }}</a>
                                        @endforeach

                                    </nav>

                                </div>

                            </div>


                            @foreach ($states as $state)
                                <div x-show="active_sub_sub_tab === '{{ $loop -> index }}'" x-transition" class="py-8">

                                    <a href="javascript:void(0)" class="button primary md"
                                        @click="add_item('{{ $company -> id }}', '{{ $recipient -> id }}', '{{ $state }}', '{{ implode(',', $states) }}')">Add Item
                                        <i class="fa-light fa-plus ml-2"></i>
                                    </a>

                                    <div x-show="active_sub_sub_tab === '{{ $loop -> index }}'" x-transition" class="p-4 space-y-2 checklist-div">
                                        {{-- blade-formatter-disable --}}
                                        @foreach ($checklist -> where('recipient_id', $recipient -> id) -> where('company_id', $company -> id) as $item )
                                            @if(stristr($item -> states, $state))
                                                <div class="flex justify-start items-center border-b py-2 space-x-4 checklist-item" data-id="{{ $item -> id }}">
                                                    <div class="item-handle">
                                                        <a href="javascript:void(0)" class="block"><i class="fa-light fa-bars"></i></a>
                                                    </div>
                                                    <div>
                                                        {!! $item -> data !!}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        {{-- blade-formatter-enable --}}
                                    </div>



                                </div>
                            @endforeach

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    @endforeach

</div>
