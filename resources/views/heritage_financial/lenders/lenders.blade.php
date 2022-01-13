@php
$title = 'Lenders';
$breadcrumbs = [
    ['Heritage Financial', ''],
    [$title, ''],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="lenders()">

        <div class="w-full mx-auto">

            <div class="">

                <div class="w-full">

                    @if(auth() -> user() -> level != 'loan_officer')
                    <div class="mt-4 mr-8 float-right">
                        <button @click="window.location='/heritage_financial/lenders/view_lender'" class="button primary lg"><i class="fal fa-plus mr-2"></i> Add Lender</button>
                    </div>
                    @endif

                    <div
                    x-data="table({
                        'container': $refs.container,
                        'data_url': '/heritage_financial/lenders/get_lenders',
                        'length': '25',
                        'sort_by': 'company_name',
                        'fields': {
                            '1': {
                                type: 'select',
                                db_field: 'active',
                                label: 'Active',
                                value: 'yes',
                                options: [
                                    ['all', 'All'],
                                    ['yes', 'Yes'],
                                    ['no', 'No'],
                                ]
                            }
                        }
                    })">

                        <div x-ref="container"></div>

                    </div>

                </div>

            </div>

        </div>

        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
        :modalTitle="'Email Lenders'"
        :modalId="'email_modal'"
        x-show="email_modal">

        Stuff goes here

        </x-modals.modal>

    </div>

</x-app-layout>
