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

    <x-nav.menu
    :level="'1'"
    :title="'Commission Reports'"
    :link="'/heritage_financial/loans/commission_reports'"
    :icon="'fad fa-money-check'"/>

    {{-- Loan Software --}}
    <x-nav.menu
    :level="'1'"
    :title="'Lending Pad/Floify'"
    :link="'/heritage_financial/loan_software'"
    :icon="'fad fa-save'"/>

    @if(auth() -> user() -> level == 'manager')
    {{-- Loans --}}
    <x-nav.menu
    :level="'1'"
    :title="'Loan Officers'"
    :link="'/employees/loan_officer'"
    :icon="'fad fa-users'"/>
    @endif

    {{-- Profile --}}
    <x-nav.menu
    :level="'1'"
    :title="'Profile'"
    :link="'/employees/profile/'"
    :icon="'fad fa-user'"/>

</ul>
