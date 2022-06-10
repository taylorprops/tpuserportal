<ul class="">

    {{-- Dashboard --}}
    <x-nav.menu :level="'1'"
        :title="'Dashboard'"
        :link="'/dashboard'"
        :icon="'fad fa-tachometer-alt'" />


    {{-- Heritage Financial --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Heritage Financial</div>
    </li>

    <x-nav.menu :level="'1'"
        :title="'Loans'"
        :link="'/heritage_financial/loans'"
        :icon="'fad fa-copy'" />

    {{-- Lenders --}}
    <x-nav.menu :level="'1'"
        :title="'Lenders'"
        :link="'/heritage_financial/lenders'"
        :icon="'fad fa-sack-dollar'" />


    @if (in_array(auth() -> user() -> level, ['manager', 'super_admin']))
        {{-- Manager Bonus --}}
        <x-nav.menu :level="'1'"
            :title="'Manager Bonuses'"
            :link="'/heritage_financial/manager_bonuses'"
            :icon="'fad fa-money-bill-wave'" />
    @endif

    {{-- End Heritage Financial --}}

    {{-- Marketing --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Marketing</div>
    </li>

    <x-nav.menu :level="'1'"
        :title="'Schedule'"
        :link="'/marketing/schedule'"
        :icon="'fa-duotone fa-calendar'" />

    {{-- End Marketing --}}

    {{-- Reports --}}
    <li>
        <div class="text-gray-700 font-semibold pl-2 pt-1 pb-2 text-sm border-t mt-3">Reports</div>
    </li>

    <x-nav.menu :level="'1'"
        :title="'Reports'"
        :link="'/reports'"
        :icon="'fad fa-chart-bar'" />
    {{-- End Reports --}}




</ul>
