@php
$title = 'Users';
$breadcrumbs = [
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2"
    x-data="employees()">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12">

            <div class="flex flex-col">

                <div class="sm:-mx-6 lg:-mx-8"
                x-data="table({
                    'container': $el,
                    'data_url': '/users/get_users',
                    'length': '10',
                    'sort_by': 'last_name',
                    'button_export': true,
                    'fields': {
                        '1': {
                            type: 'select',
                            db_field: 'active',
                            label: 'Active',
                            value: 'yes',
                            options: [
                                ['yes', 'Yes'],
                                ['no', 'No'],
                            ]
                        }
                    }
                })">

                </div>

            </div>

        </div>



        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
        :modalTitle="'Confirm Reset Password'"
        :modalId="'show_confirm_reset_password'"
        x-show="show_confirm_reset_password">

            <div class="text-center pt-4">
                Please confirm you would like to email a password reset link to <br>
                <span class="user-name-reset-password font-semibold text-lg"></span>
            </div>

            <div class="flex justify-around p-4 pt-6">
                <button type="button" class="button primary lg" @click="reset_password($el)"><i class="fal fa-check mr-2"></i> Reset Password</button>
            </div>

        </x-modals.modal>

        <x-modals.modal
        :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-1/3'"
        :modalTitle="'Confirm Send Welcome Email'"
        :modalId="'show_confirm_send_welcome_email'"
        x-show="show_confirm_send_welcome_email">

        <div class="text-center pt-4">
            Please confirm you would like to send the welcome email to<br>
            <span class="user-name-send-welcome-email font-semibold text-lg"></span>
        </div>

            <div class="flex justify-around p-4 pt-6">
                <button type="button" class="button primary lg" @click="send_welcome_email($el)"><i class="fal fa-check mr-2"></i> Send Welcome Email</button>
            </div>

        </x-modals.modal>

    </div>

</x-app-layout>
