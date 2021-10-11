<ul class="overflow-visible z-50 d-block">

    {{-- Dashboard --}}
    <x-nav.menu
    :level="'1'"
    :title="'Dashboard'"
    :link="'/dashboard'"
    :icon="'fad fa-tachometer-alt'"/>


    {{-- Doc Management --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-3 pb-2 text-sm border-t mt-3">Doc Management</div>
    </li>

    {{-- Transactions --}}
    @php
    $level2 = [
        [
            'title' => 'View Transactions',
            'link' => '/transactions',
            'icon' => 'fad fa-bars mr-2'
        ],
        [
            'title' => 'Add Listing',
            'link' => '/transactions/create/listing',
            'icon' => 'fad fa-plus mr-2'
        ],
        [
            'title' => 'Add Contract',
            'link' => '/transactions/create/contract',
            'icon' => 'fad fa-plus mr-2'
        ],
        [
            'title' => 'Add Referral',
            'link' => '/transactions/create/referral',
            'icon' => 'fad fa-plus mr-2'
        ],
        [
            'title' => 'Archived Transactions',
            'link' => '/transactions_archived',
            'icon' => 'fad fa-file-archive mr-2'
        ],
    ];

    @endphp

    <x-nav.menu
    :level="'2'"
    :title="'Transactions'"
    :icon="'fad fa-sign'"
    :level2="$level2"/>

    {{-- End Transactions --}}



    {{-- Admin --}}

    @php
    $level3 = [
        [
            'title' => 'Forms',
            'link' => '/doc_management/admin/forms/forms',
            'icon' => 'far fa-book mr-2'
        ],
        [
            'title' => 'Checklists',
            'link' => '/doc_management/admin/checklists/checklists',
            'icon' => 'fad fa-tasks mr-2'
        ],
        [
            'title' => 'Resources',
            'icon' => 'fad fa-tools mr-2',
            'sub_links' => [
                [
                    'title' => 'Site Resources',
                    'link' => '/doc_management/site_resources',
                    'icon' => 'fad fa-circle mr-2'
                ],
                [
                    'title' => 'Common Fields',
                    'link' => '/doc_management/common_fields',
                    'icon' => 'fad fa-object-ungroup mr-2'
                ]
            ]
        ]
    ];

    @endphp

    <x-nav.menu
    :level="'3'"
    :title="'Admin'"
    :icon="'far fa-user-lock'"
    :level3="$level3"/>

    {{-- End Admin --}}


    {{-- Escrow --}}
    <x-nav.menu
    :level="'1'"
    :title="'Escrow'"
    :link="'/transactions_archived/escrow'"
    :icon="'fad fa-money-check'"/>

    {{-- End Doc Management --}}


    {{-- Employees --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-3 pb-2 text-sm border-t mt-3">Employees</div>
    </li>

        @php
        $level2 = [
            [
                'title' => 'In-House',
                'link' => '/employees/in_house',
                'icon' => 'fad fa-house-user mr-2'
            ],
            [
                'title' => 'Agents',
                'link' => '/employees/agent',
                'icon' => 'fad fa-person-sign mr-2'
            ],
            [
                'title' => 'Loan Officers',
                'link' => '/employees/loan_officer',
                'icon' => 'fad fa-user-chart mr-2'
            ],

        ];

        @endphp

        <x-nav.menu
        :level="'2'"
        :title="'Employees'"
        :icon="'fad fa-users'"
        :level2="$level2"/>


    {{-- End Employees --}}


    @if(auth() -> user() -> level == 'super_admin')
    {{-- Super Admin --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-3 pb-2 text-sm border-t mt-3">Super Admin</div>
    </li>

        @php
        $level2 = [
            [
                'title' => 'Queue Monitor',
                'link' => '/admin/monitor',
                'icon' => 'fad fa-analytics mr-2'
            ],
            [
                'title' => 'Form Elements',
                'link' => '/resources/design/form_elements',
                'icon' => 'fad fa-rectangle-wide mr-2'
            ],
            [
                'title' => 'Config Variables',
                'link' => '/resources/config/config_variables',
                'icon' => 'fad fa-cogs mr-2'
            ]
        ];

        @endphp

        <x-nav.menu
        :level="'2'"
        :title="'Super Admin'"
        :icon="'fad fa-globe'"
        :level2="$level2"/>

    {{-- End Super Admin --}}
    @endif

</ul>
