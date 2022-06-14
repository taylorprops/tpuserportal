<div>
    <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
            @change="active_tab = $el.value">
            <option value="1" selected>Taylor Properties</option>
            <option value="2">Heritage Title</option>
            <option value="3">Heritage Financial</option>
        </select>
    </div>
    <div class="hidden sm:block">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-12" aria-label="Tabs">
                <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '1', 'border-primary text-primary-dark': active_tab === '1' }"
                    @click="active_tab = '1'">Taylor Properties </a>
                <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '2', 'border-primary text-primary-dark': active_tab === '2' }"
                    @click="active_tab = '2'"> Heritage Title </a>
                <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_tab !== '3', 'border-primary text-primary-dark': active_tab === '3' }"
                    @click="active_tab = '3'"> Heritage Financial</a>
            </nav>
        </div>
    </div>

    <div x-show="active_tab === '1'" x-transition"
        class="p-4 border"
        x-data="{ active_sub_tab: '1' }">

        <div>
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                    @change="active_sub_tab = $el.value">
                    <option value="1" selected>Agents - Recruiting - Outside</option>
                    <option value="2">Agents - Recruiting - PSI</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-12" aria-label="Tabs">
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '1', 'border-primary text-primary-dark': active_sub_tab === '1' }"
                            @click="active_sub_tab = '1'">Agents - Recruiting - Outside </a>
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '2', 'border-primary text-primary-dark': active_sub_tab === '2' }"
                            @click="active_sub_tab = '2'">Agents - Recruiting - PSI</a>
                    </nav>
                </div>
            </div>

            <div x-show="active_sub_tab === '1'" x-transition"
                class="p-4">
                <a href="javascript:void(0)" class="button primary md" @click="add_item('1', '4')">Add Item <i class="fa-light fa-plus ml-2"></i></a>
            </div>

            <div x-show="active_sub_tab === '2'" x-transition"
                class="p-4">
                2
            </div>


        </div>

    </div>

    <div x-show="active_tab === '2'" x-transition"
        class="p-4 border"
        x-data="{ active_sub_tab: '1' }">

        <div>
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                    @change="active_sub_tab = $el.value">
                    <option value="1" selected>Loan Officers - Recruiting</option>
                    <option value="2">Agents - Business - Outside</option>
                    <option value="3">Agents - Business - In House</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-12" aria-label="Tabs">
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '1', 'border-primary text-primary-dark': active_sub_tab === '1' }"
                            @click="active_sub_tab = '1'">Loan Officers - Recruiting </a>
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '2', 'border-primary text-primary-dark': active_sub_tab === '2' }"
                            @click="active_sub_tab = '2'">Agents - Business - Outside</a>
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '3', 'border-primary text-primary-dark': active_sub_tab === '3' }"
                            @click="active_sub_tab = '3'">Agents - Business - In House</a>
                    </nav>
                </div>
            </div>

            <div x-show="active_sub_tab === '1'" x-transition"
                class="p-4">
                1
            </div>

            <div x-show="active_sub_tab === '2'" x-transition"
                class="p-4">
                2
            </div>

            <div x-show="active_sub_tab === '3'" x-transition"
                class="p-4">
                3
            </div>


        </div>

    </div>

    <div x-show="active_tab === '3'" x-transition"
        class="p-4 border"
        x-data="{ active_sub_tab: '1' }">

        <div>
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                    @change="active_sub_tab = $el.value">
                    <option value="1" selected>Loan Officers - Recruiting</option>
                    <option value="2">Agents - Business - Outside</option>
                    <option value="3">Agents - Business - In House</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-12" aria-label="Tabs">
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '1', 'border-primary text-primary-dark': active_sub_tab === '1' }"
                            @click="active_sub_tab = '1'">Loan Officers - Business </a>
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '2', 'border-primary text-primary-dark': active_sub_tab === '2' }"
                            @click="active_sub_tab = '2'">Agents - Business - Outside</a>
                        <a href="javascript:void(0)" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            :class="{ 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': active_sub_tab !== '3', 'border-primary text-primary-dark': active_sub_tab === '3' }"
                            @click="active_sub_tab = '3'">Agents - Business - In House</a>
                    </nav>
                </div>
            </div>

            <div x-show="active_sub_tab === '1'" x-transition"
                class="p-4">
                1
            </div>

            <div x-show="active_sub_tab === '2'" x-transition"
                class="p-4">
                2
            </div>

            <div x-show="active_sub_tab === '3'" x-transition"
                class="p-4">
                3
            </div>


        </div>

    </div>

</div>
