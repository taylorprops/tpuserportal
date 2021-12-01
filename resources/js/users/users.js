const { default: Toastr } = require("toastr2");

if(document.URL.match(/users$/)) {


    window.employees = function() {

        return {
            search_val: '',
            active_url: '/users/get_users',
            page_url: '/users/get_users',
            active: 'yes',
            sort: 'last_name',
            table: '.employees-table',
            show_confirm_reset_password: false,
            show_confirm_send_welcome_email: false,
            reset_password_id: '',
            send_welcome_email_id: '',
            init() {
                show_loading();
                this.show_active('yes');
            },
            get_data(url) {

                let scope = this;
                if(!url) {
                    url = scope.page_url;
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector(scope.table).innerHTML = response.data;
                    scope.links(scope.table);
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            links(table) {
                let scope = this;
                let container = document.querySelector(table);
                let container_table = container.querySelector('table');
                container_table.querySelectorAll('th').forEach(function(th) {
                    if(th.querySelector('a')) {
                        th.querySelector('a').classList.add('sort-by');
                    }
                });

                document.querySelector(table).querySelectorAll('a').forEach(function(link) {
                    if(!link.classList.contains('view-link')) {
                        link.addEventListener('click', function(e) {

                            show_loading();
                            e.preventDefault();

                            let url = this.href+'&length='+scope.length;
                            let href = new URL(url);
                            let params = new URLSearchParams(href.search);

                            if(this.search_val != '') {
                                if(url.match(/\?/)) {
                                    url += '&';
                                } else {
                                    url += '?';
                                }

                                params.delete('search');
                                url += 'search=' + scope.search_val;
                            }

                            if(link.classList.contains('sort-by')) {
                                scope.sort = params.get('sort');
                            }

                            url += '&sort=' + scope.sort;
                            scope.active_url = url;
                            scope.get_data(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active = '';
                document.querySelector('#active').value = '';
                this.active_url = this.page_url+'?search=' + val.trim();
                this.get_data(this.active_url);
            },
            show_active(active) {
                this.active = active;
                this.active_url = this.active_url.replace(/[&]*active=[a-z]*/, '');
                if(this.active_url.match(/\?/)) {
                    this.active_url += '&';
                } else {
                    this.active_url += '?';
                }
                this.active_url += 'active=' + active;
                this.get_data(this.active_url);
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
