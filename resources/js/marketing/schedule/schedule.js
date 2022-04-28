if(document.URL.match('marketing/schedule')) {

    window.schedule = function() {

        return {

            show_item_modal: false,
            show_html: false,
            show_file: false,
            add_event: false,
            edit_event: false,
            show_versions_modal: false,
            show_add_version_modal: false,

            init() {
                this.get_schedule();
            },

            get_schedule() {
                let scope = this;
                axios.get('/marketing/get_schedule')
                .then(function (response) {
                    scope.$refs.schedule_list_div.innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            save_item(ele) {
                let scope = this;

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = scope.$refs.schedule_form;
                let formData = new FormData(form);
                let action = 'add';
                if(scope.$refs.id.value != '') {
                    action = 'edit';
                }

                axios.post('/marketing/save_item', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;
                    toastr.success('Item Successfully Added');
                    scope.get_schedule();
                    scope.show_item_modal = false;
                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
            },

            show_view_div(type, file, html) {
                let scope = this;
                if(html) {
                    scope.show_html = true;
                } else {
                    scope.show_file = true;
                    scope.$refs.view_file.setAttribute('src', file);
                    if(type == 'image') {
                        scope.$refs.view_file.setAttribute('height', 'auto');
                    } else if(type == 'pdf') {
                        scope.$refs.view_file.setAttribute('height', '100vh');
                    }
                }
            },

            edit_item(ele) {

                let scope = this;
                scope.$refs.id.value = ele.getAttribute('data-id');
                scope.$refs.event_date.value = ele.getAttribute('data-event-date');
                let states = ele.getAttribute('data-state').split(',');
                states.forEach(function(state) {
                    document.querySelector('#'+state).checked = false;
                    document.querySelector('#'+state).click();
                });
                scope.$refs.recipient_id.value = ele.getAttribute('data-recipient-id');
                scope.$refs.company_id.value = ele.getAttribute('data-company-id');
                console.log(scope.$refs.medium_id);
                scope.$refs.medium_id.value = ele.getAttribute('data-medium-id');
                scope.$refs.description.value = ele.getAttribute('data-description');

            },

            show_versions(id) {
                let scope = this;
                axios.get('/marketing/show_versions', {
                    params: {
                        id: id
                    },
                })
                .then(function (response) {
                    scope.show_versions_modal = true;
                    scope.$refs.versions_div.innerHTML = response.data;
                    setTimeout(function() {
                        document.querySelectorAll('.version-iframe').forEach(function(iframe) {
                            let html = iframe.innerHTML;
                            iframe.innerHTML = '';
                            iframe = iframe.contentWindow || ( iframe.contentDocument.document || iframe.contentDocument);
                            iframe.document.open();
                            iframe.document.write(html);
                            iframe.document.close();
                        });
                    }, 1000);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            add_version(id) {

                let scope = this;
                scope.show_add_version_modal = true;

            },

        }

    }


}
