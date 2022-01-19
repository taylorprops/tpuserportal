<ul class="">

    {{-- Dashboard --}}
    <x-nav.menu
    :level="'1'"
    :title="'Dashboard'"
    :link="'/dashboard'"
    :icon="'fad fa-tachometer-alt'"/>

    {{-- Loans --}}
    <x-nav.menu
    :level="'1'"
    :title="'Loans'"
    :link="'/heritage_financial/loans'"
    :icon="'fad fa-copy'"/>

    @if(in_array(auth() -> user() -> level, ['loan_officer']))
    <x-nav.menu
    :level="'1'"
    :title="'Commission Reports'"
    :link="'/heritage_financial/loans/commission_reports'"
    :icon="'fad fa-money-check'"/>
    @endif

    {{-- Loan Software --}}
    <x-nav.menu
    :level="'1'"
    :title="'Lending Pad/Floify'"
    :link="'/heritage_financial/loan_software'"
    :icon="'fad fa-save'"/>

    {{-- Lenders --}}
    <x-nav.menu
    :level="'1'"
    :title="'Lenders'"
    :link="'/heritage_financial/lenders'"
    :icon="'fad fa-sack-dollar'"/>

    @if(in_array(auth() -> user() -> level, ['manager', 'processor']))
    {{-- Loans --}}
    <x-nav.menu
    :level="'1'"
    :title="'Loan Officers'"
    :link="'/employees/loan_officer'"
    :icon="'fad fa-users'"/>
    @endif

    @if(auth() -> user() -> level == 'manager')
    {{-- Manager Bonus --}}
    <x-nav.menu
    :level="'1'"
    :title="'Manager Bonuses'"
    :link="'/heritage_financial/manager_bonuses'"
    :icon="'fad fa-money-bill-wave'"/>

    {{-- Reports --}}
    <x-nav.menu
    :level="'1'"
    :title="'Reports'"
    :link="'/reports'"
    :icon="'fad fa-chart-bar'"/>
    {{-- End Reports --}}
    @endif

    {{-- Profile --}}
    <x-nav.menu
    :level="'1'"
    :title="'Profile'"
    :link="'/employees/profile/'"
    :icon="'fad fa-user'"/>

</ul>
