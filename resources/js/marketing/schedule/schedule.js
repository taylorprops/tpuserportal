
if (document.URL.match('marketing/schedule')) {

    window.schedule = function () {

        return {

            show_item_modal: false,
            show_calendar: true,
            show_html: false,
            show_file: false,
            add_event: false,
            edit_event: false,
            show_versions_modal: false,
            show_add_version_modal: false,
            show_delete_event_modal: false,
            show_email_options: false,

            init() {
                this.get_schedule();

            },

            get_schedule(id = null) {

                let scope = this;
                let form = scope.$refs.filter_form;
                let formData = new FormData(form);

                axios.post('/marketing/get_schedule', formData)
                .then(function (response) {
                    scope.$refs.schedule_list_div.innerHTML = response.data;
                    scope.calendar();
                    if(id) {
                        let event_div = document.querySelector('#event_'+id);
                        event_div.classList.add('cloned');
                        setTimeout(function() {
                            event_div.querySelector('.edit-button').click();
                        }, 500);
                    }
                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });

            },

            clear_form() {
                this.$refs.schedule_form.reset();
            },

            save_item(ele) {
                let scope = this;

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = scope.$refs.schedule_form;
                let formData = new FormData(form);
                let action = 'add';
                if (scope.$refs.id.value != '') {
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
                scope.show_calendar = false;
                scope.show_file = false;
                scope.show_html = false;
                if (html) {
                    scope.show_html = true;
                    let iframe = document.querySelector('.view-accepted-iframe');
                    iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe.contentDocument);
                    iframe.document.open();
                    iframe.document.write(html);
                    iframe.document.close();
                } else {
                    scope.show_file = true;
                    scope.$refs.view_file.setAttribute('src', file);
                    if (type == 'image') {
                        scope.$refs.view_file.setAttribute('height', 'auto');
                    } else if (type == 'pdf') {
                        scope.$refs.view_file.setAttribute('height', '100vh');
                    }
                }
            },

            edit_item(ele) {

                let scope = this;
                let id = ele.getAttribute('data-id');
                scope.$refs.id.value = id;
                scope.$refs.event_date.value = ele.getAttribute('data-event-date');
                let states = ele.getAttribute('data-state').split(',');
                states.forEach(function (state) {
                    document.querySelector('#' + state).checked = false;
                    document.querySelector('#' + state).click();
                });
                scope.$refs.recipient_id.value = ele.getAttribute('data-recipient-id');
                scope.$refs.company_id.value = ele.getAttribute('data-company-id');
                scope.$refs.medium_id.value = ele.getAttribute('data-medium-id');
                scope.$refs.description.value = ele.getAttribute('data-description');
                scope.$refs.subject_line_a.value = ele.getAttribute('data-subject-line-a');
                scope.$refs.subject_line_b.value = ele.getAttribute('data-subject-line-b');
                scope.$refs.preview_text.value = ele.getAttribute('data-preview-text');
                scope.show_email_options = false;
                if(scope.$refs.medium_id.options[scope.$refs.medium_id.selectedIndex].text == 'Email') {
                    scope.show_email_options = true;
                }
                scope.$refs.delete_event_button.addEventListener('click', function() {
                    scope.show_delete_event(id, scope.$refs.delete_event_button);
                });

                scope.$refs.show_versions_button.addEventListener('click', function() {
                    scope.show_versions(id);
                });

            },

            show_delete_event(id, ele) {

                let scope = this;
                scope.show_delete_event_modal = true;

                scope.$refs.delete_event.addEventListener('click', function() {

                    let button_html = ele.innerHTML;
                    show_loading_button(ele, 'Deleting ... ');

                    let formData = new FormData();
                    formData.append('id', id);

                    axios.post('/marketing/delete_event', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.get_schedule();
                        scope.show_delete_event_modal = false;
                        toastr.error('Event Successfully Recycled');
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

                });
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
                        setTimeout(function () {
                            document.querySelectorAll('.version-iframe').forEach(function (iframe) {
                                let html = iframe.innerHTML;
                                iframe.innerHTML = '';
                                iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe.contentDocument);
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
                document.querySelector('#add_version_id').value = id;

            },

            save_version_item(ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = scope.$refs.add_version_form;
                let formData = new FormData(form);

                axios.post('/marketing/save_add_version', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;

                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
            },

            clone(id, ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Cloning ... ');

                let formData = new FormData();
                formData.append('id', id);

                axios.post('/marketing/clone_event', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;
                    let id = response.data.id;
                    scope.get_schedule(id);
                })
                .catch(function (error) {
                });

            },

            calendar() {

                axios.get('/marketing/calendar_get_events')
                .then(function (response) {

                    let calendarEl = document.querySelector('.calendar');
                    let calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        events: response.data,
                        eventColor: 'rgb(63 98 156)',
                        eventTextColor: '#fff',
                        eventClick: function(info) {
                            let id = info.event.id;
                            document.querySelector('.edit-button[data-id="'+id+'"]').click();
                        }
                    });

                    calendar.render();

                })
                .catch(function (error) {
                    console.log(error);
                });



            },

            get_html_from_link(ele, textarea) {
                show_loading();
                textarea.value = '';
                axios.get(ele.value)
                .then(function (response) {
                    textarea.value = response.data;
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                    hide_loading();
                    toastr.error('URL Not Found');
                });
            },

        }

    }


}
