

<x-nav.menu
    :level="'1'"
    :title="'Dashboard'"
    :link="'/dashboard'"
    :icon="'fad fa-tachometer-alt'"/>


<div class="text-white font-semibold pl-2 py-3 text-sm border-t border-white">Doc Management</div>

@php
$level2 = [
    [
        'title' => 'View Transactions',
        'link' => '/transactions',
        'icon' => 'fal fa-bars mr-2'
    ],
    [
        'title' => 'Add Listing',
        'link' => '/transactions/create/listing',
        'icon' => 'fal fa-plus mr-2'
    ],
    [
        'title' => 'Add Contract',
        'link' => '/transactions/create/contract',
        'icon' => 'fal fa-plus mr-2'
    ],
    [
        'title' => 'Add Referral',
        'link' => '/transactions/create/referral',
        'icon' => 'fal fa-plus mr-2'
    ]
];

@endphp

<x-nav.menu
    :level="'2'"
    :title="'Transactions'"
    :icon="'fad fa-sign'"
    :level2="$level2"/>

@php
$level3 = [
    [
        'title' => 'Forms',
        'link' => '/doc_management/admin/forms/forms',
        'icon' => 'far fa-book mr-2'
    ],
    [
        'title' => 'Checklists',
        'link' => '/doc_management/checklists',
        'icon' => 'fal fa-tasks mr-2'
    ],
    [
        'title' => 'Resources',
        'icon' => 'fal fa-tools mr-2',
        'sub_links' => [
            [
                'title' => 'Site Resources',
                'link' => '/doc_management/site_resources',
                'icon' => 'fal fa-circle mr-2'
            ],
            [
                'title' => 'Common Fields',
                'link' => '/doc_management/common_fields',
                'icon' => 'fal fa-object-ungroup mr-2'
            ]
        ]
    ]
];

@endphp

<x-nav.menu
    :level="'3'"
    :title="'Admin'"
    :icon="'far fa-tasks-alt'"
    :level3="$level3"/>



@php
$level2 = [
    [
        'title' => 'Form Elements',
        'link' => '/resources/design/form_elements',
        'icon' => 'fad fa-rectangle-wide mr-2'
    ]
];

@endphp

<x-nav.menu
    :level="'2'"
    :title="'Super Admin'"
    :icon="'fad fa-globe'"
    :level2="$level2"/>
