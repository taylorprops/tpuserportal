<ul class="w-full border border-gray-100 animate__animated animate__fadeIn">

    @foreach($form_groups as $form_group)

        @php
        $form_group_id = $form_group -> id;
        $forms = $form_group -> forms;
        @endphp

        <li class="form-group-li border border-b p-3 w-full"
            x-show="active_tab === '{{ $form_group_id }}'"
            :class="{ 'active' : active_tab === '{{ $form_group_id }}' }">

            <div class="flex justify-between items-center mb-5">

                <div class="text-gray-700 font-bold text-lg">
                    {{ $form_group -> group_name }}
                </div>

                <div class="flex justify-between items-center">

                    <div>
                        <x-elements.select
                        class="sort-by"
                        name=""
                        data-label="Sort By"
                        :size="'md'"
                        x-on:change="get_forms()">
                            <option value="created_at" selected>Date Added</option>
                            <option value="form_name">A-Z</option>
                        </x-elements.select>
                    </div>

                    <div class="ml-2">
                        <x-elements.select
                        class="show-published"
                        name=""
                        data-label="Published"
                        :size="'md'"
                        x-on:change="get_forms()">
                            <option value="" selected>All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </x-elements.select>
                    </div>

                    <div class="ml-2">
                        <x-elements.select
                        class="show-active"
                        name=""
                        data-label="Active"
                        :size="'md'"
                        x-on:change="get_forms()">
                            <option value="">All</option>
                            <option value="yes" selected>Active</option>
                            <option value="no">Deleted</option>
                        </x-elements.select>
                    </div>

                </div>


            </div>

            <div class="h-screen-70 overflow-auto" id="form_group_{{ $form_group_id }}"></div>

        </li>

    @endforeach

</ul>
