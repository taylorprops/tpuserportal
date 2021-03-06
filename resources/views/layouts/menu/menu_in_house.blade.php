<ul class="">

    {{-- Dashboard --}}
    <x-nav.menu :level="'1'" :title="'Dashboard'" :link="'/dashboard'" :icon="'fad fa-tachometer-alt'" />


    @if (auth() -> user() -> level == 'super_admin')
        {{-- Doc Management --}}
        <li>
            <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Doc Management</div>
        </li>

        {{-- Transactions --}}
        {{-- blade-formatter-disable --}}
        @php
            $level2 = [
                [
                    'title' => 'View Transactions',
                    'link' => '/transactions',
                    'icon' => 'fad fa-bars mr-2',
                ],
                [
                    'title' => 'Add Listing',
                    'link' => '/transactions/create/listing',
                    'icon' => 'fad fa-plus mr-2',
                ],
                [
                    'title' => 'Add Contract',
                    'link' => '/transactions/create/contract',
                    'icon' => 'fad fa-plus mr-2',
                ],
                [
                    'title' => 'Add Referral',
                    'link' => '/transactions/create/referral',
                    'icon' => 'fad fa-plus mr-2',
                ],
            ];

        @endphp
        {{-- blade-formatter-enable --}}

        <x-nav.menu :level="'2'" :title="'Transactions'" :icon="'fad fa-sign'" :level2="$level2" />

        {{-- End Transactions --}}



        {{-- Admin --}}

        {{-- blade-formatter-disable --}}
        @php
            $level3 = [
                [
                    'title' => 'Forms',
                    'link' => '/doc_management/admin/forms/forms',
                    'icon' => 'far fa-book mr-2',
                ],
                [
                    'title' => 'Checklists',
                    'link' => '/doc_management/admin/checklists/checklists',
                    'icon' => 'fad fa-tasks mr-2',
                ],
                [
                    'title' => 'Resources',
                    'icon' => 'fad fa-tools mr-2',
                    'sub_links' => [
                        [
                            'title' => 'Site Resources',
                            'link' => '/doc_management/site_resources',
                            'icon' => 'fad fa-circle mr-2',
                        ],
                        [
                            'title' => 'Common Fields',
                            'link' => '/doc_management/common_fields',
                            'icon' => 'fad fa-object-ungroup mr-2',
                        ],
                    ],
                ],
            ];

        @endphp
{{-- blade-formatter-enable --}}

        <x-nav.menu :level="'3'" :title="'Admin'" :icon="'far fa-user-lock'" :level3="$level3" />

        {{-- End Admin --}}

        {{-- End Doc Management --}}
    @endif


    {{-- Heritage Financial --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Heritage Financial</div>
    </li>

    <x-nav.menu :level="'1'" :title="'Loans'" :link="'/heritage_financial/loans'" :icon="'fad fa-copy'" />

    {{-- Loan Software --}}
    <x-nav.menu :level="'1'" :title="'Lending Pad/Floify'" :link="'/heritage_financial/loan_software'" :icon="'fad fa-desktop'" />

    {{-- Lenders --}}
    <x-nav.menu :level="'1'" :title="'Lenders'" :link="'/heritage_financial/lenders'" :icon="'fad fa-sack-dollar'" />

    <x-nav.menu :level="'1'" :title="'Agent Database'" :link="'/heritage_financial/agent_database'" :icon="'fad fa-database'" />

    @if (in_array(auth() -> user() -> level, ['manager', 'super_admin']))
        {{-- Manager Bonus --}}
        <x-nav.menu :level="'1'" :title="'Manager Bonuses'" :link="'/heritage_financial/manager_bonuses'" :icon="'fad fa-money-bill-wave'" />
    @endif

    {{-- End Heritage Financial --}}

    {{-- Employees --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Employees/Users</div>
    </li>

    {{-- blade-formatter-disable --}}
    @php
        $level2 = [
            [
                'title' => 'In-House',
                'link' => '/employees/in_house',
                'icon' => 'fad fa-house-user mr-2',
            ],
            [
                'title' => 'Agents',
                'link' => '/employees/agent',
                'icon' => 'fad fa-person-sign mr-2',
            ],
            [
                'title' => 'Mortgage',
                'link' => '/employees/loan_officer',
                'icon' => 'fad fa-user-chart mr-2',
            ],
        ];

    @endphp
{{-- blade-formatter-enable --}}

    @if (auth() -> user() -> level == 'super_admin')
        <x-nav.menu :level="'2'" :title="'Employees'" :icon="'fad fa-users'" :level2="$level2" />
    @endif

    <x-nav.menu :level="'1'" :title="'Website Users'" :link="'/users'" :icon="'fad fa-users-cog'" />


    {{-- End Employees --}}


    {{-- Marketing --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Marketing</div>
    </li>

    <x-nav.menu :level="'1'" :title="'Schedule'" :link="'/marketing/schedule'" :icon="'fa-duotone fa-calendar'" />

    @if (auth() -> user() -> level == 'super_admin' || auth() -> user() -> level == 'marketing')
        <x-nav.menu :level="'1'" :title="'Marketing Checklist'" :link="'/marketing/schedule/checklist'" :icon="'fa-duotone fa-check'" />

        <x-nav.menu :level="'1'" :title="'Marketing Notes'" :link="'/marketing/marketing_notes'" :icon="'fa-duotone fa-notes'" />


        <x-nav.menu :level="'1'" :title="'Address Database'" :link="'/marketing/data/address_database'" :icon="'fad fa-database'" />

        <x-nav.menu :level="'1'" :title="'Upload List'" :link="'/marketing/data/upload_list'" :icon="'fa-duotone fa-upload'" />
    @endif

    @if (auth() -> user() -> level == 'super_admin')
        <x-nav.menu :level="'1'" :title="'Schedule Settings'" :link="'/marketing/schedule_settings'" :icon="'fa-duotone fa-gears'" />
    @endif

    {{-- End Marketing --}}

    {{-- Reports --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Reports</div>
    </li>

    <x-nav.menu :level="'1'" :title="'Reports'" :link="'/reports'" :icon="'fad fa-chart-bar'" />
    {{-- End Reports --}}

    {{-- Notes --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Notes</div>
    </li>

    <x-nav.menu :level="'1'" :title="'Notes'" :link="'/notes'" :icon="'fad fa-chart-bar'" />
    {{-- End Notes --}}

    {{-- Archives --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Archives</div>
    </li>

    <x-nav.menu :level="'1'" :title="'Transactions'" :link="'/transactions_archived'" :icon="'fad fa-file-archive'" />

    <x-nav.menu :level="'1'" :title="'Escrow'" :link="'/transactions_archived/escrow'" :icon="'fad fa-money-check'" />

    {{-- End Archives --}}

    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">My Account</div>
    </li>
    {{-- Profile --}}
    <x-nav.menu :level="'1'" :title="'Profile'" :link="'/employees/profile/'" :icon="'fad fa-user'" />


    @if (auth() -> user() -> level == 'super_admin')
        {{-- Super Admin --}}
        <li>
            <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Super Admin</div>
        </li>

        {{-- blade-formatter-disable --}}
        @php
            $level2 = [
                [
                    'title' => 'System Monitor',
                    'link' => '/admin/system_monitor',
                    'icon' => 'fad fa-dashboard mr-2',
                ],
                [
                    'title' => 'Queue Monitor',
                    'link' => '/admin/queue_monitor',
                    'icon' => 'fad fa-analytics mr-2',
                ],
                [
                    'title' => 'Form Elements',
                    'link' => '/resources/design/form_elements',
                    'icon' => 'fad fa-rectangle-wide mr-2',
                ],
                [
                    'title' => 'Config Variables',
                    'link' => '/resources/config/config_variables',
                    'icon' => 'fad fa-cogs mr-2',
                ],
                [
                    'title' => 'Tools',
                    'link' => '/tools/tools',
                    'icon' => 'fad fa-cogs mr-2',
                ],
            ];

        @endphp
{{-- blade-formatter-enable --}}

        <x-nav.menu :level="'2'" :title="'Super Admin'" :icon="'fad fa-globe'" :level2="$level2" />

        {{-- End Super Admin --}}
    @endif

</ul>
