
if(document.URL.match(/users$/)) {


    window.employees = function() {

        return {

            show_confirm_reset_password: false,
            show_confirm_send_welcome_email: false,
            reset_password_id: '',
            send_welcome_email_id: '',

            search_val: '',
            active_url: '/users/get_users',
            page_url: '/users/get_users',
            active: 'yes',
            sort: 'last_name',
            table: '.employees-table',
            length: '10',

            init() {
                show_loading();
                table_show_active(this, 'yes');
            },
            init_table_change_length(val) {
                table_change_length(this, val);
            },
            init_table_show_active(val) {
                table_show_active(this, val);
            },
            init_table_search(val) {
                table_search(this, val);
            },

            confirm_reset_password(id, name) {
                this.show_confirm_reset_password = true;
                this.reset_password_id = id;
                document.querySelector('.user-name-reset-password').innerText = name;
            },
            confirm_send_welcome_email(id, name) {
                this.show_confirm_send_welcome_email = true;
                this.send_welcome_email_id = id;
                document.querySelector('.user-name-send-welcome-email').innerText = name;
            },
            reset_password(ele) {

                let scope = this;

                show_loading_button(ele, 'Resetting Password ... ');

                let formData = new FormData();
                formData.append('id', scope.reset_password_id);

                axios.post('/users/reset_password', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-check mr-2"></i> Reset Password';
                    toastr.success('Password reset email sent successfully');
                    scope.show_confirm_reset_password = false;
                })
                .catch(function (error) {
                });
            },
            send_welcome_email(ele) {

                let scope = this;

                show_loading_button(ele, 'sending Welcome Email ... ');

                let formData = new FormData();
                formData.append('id', scope.send_welcome_email_id);

                axios.post('/users/send_welcome_email', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-check mr-2"></i> Send Welcome Email';
                    toastr.success('Welcome email sent successfully');
                    scope.show_confirm_send_welcome_email = false;
                })
                .catch(function (error) {
                });
            },
        }


    }





}
