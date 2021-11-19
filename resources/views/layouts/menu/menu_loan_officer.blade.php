<ul class="overflow-visible z-50 d-block">

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
