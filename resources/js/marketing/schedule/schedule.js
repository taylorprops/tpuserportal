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
            show_deleted_versions: false,
            show_email_modal: false,
            show_subject_options: false,
            calendar_object: '',
            show_weekends: false,
            active_event: '',
            show_export_modal: false,

            init() {

                let view_id = global_get_url_parameters('view') || null;
                this.get_schedule(null, view_id);

            },

            get_schedule(id = null, view_id = null) {

                let scope = this;
                let form = scope.$refs.filter_form;
                let formData = new FormData(form);
                let show_completed = scope.$refs.show_completed.checked;

                formData.append('show_completed', show_completed);

                axios.post('/marketing/get_schedule', formData)
                    .then(function (response) {
                        scope.$refs.schedule_list_div.innerHTML = response.data;
                        scope.calendar();
                        if (id) {
                            let event_div = document.querySelector('#event_' + id);
                            event_div.classList.add('cloned');
                            setTimeout(function () {
                                event_div.querySelector('.edit-button').click();
                            }, 500);
                        }

                        setTimeout(function () {
                            tippy('[data-tippy-content]', {
                                allowHTML: true,
                            });
                            let options = {
                                selector: '.add-notes',
                                height: '300',
                                statusbar: false,
                                menubar: false,
                                plugins: [
                                    'advlist', 'autolink', 'lists', 'image', 'visualblocks', 'code', 'fullscreen', 'media', 'table', 'autoresize'
                                ],
                                toolbar: 'undo redo bold italic hr | forecolor backcolor | image | bullist numlist outdent indent',
                                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                                relative_urls: false,
                                document_base_url: location.hostname,
                            }
                            text_editor(options);

                            if (view_id) {
                                scope.show_view_div(view_id);
                                scope.active_event = view_id;
                                document.getElementById('event_' + view_id).previousSibling.previousSibling.scrollIntoView({ behavior: "smooth", block: "start" });
                            }

                        }, 500);
                    })
                    .catch(function (error) {
                        display_errors(error);
                    });

            },

            clear_form(form) {
                form.reset();
                document.querySelectorAll('[type="file"]').forEach(function (input) {
                    show_file_names(input);
                });
                document.querySelectorAll('.to-address').forEach(function (address) {
                    address.checked = false;
                });
                this.update_to_addresses();
            },

            clear_add_version_form() {

                let form = document.querySelector('#add_version_form');
                this.clear_form(form);
            },

            save_item(ele) {

                let scope = this;

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = scope.$refs.schedule_form;
                let formData = new FormData(form);
                let event_id = scope.$refs.id.value || null;

                axios.post('/marketing/save_item', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.show_item_modal = false;
                        scope.clear_form(form);
                        scope.get_schedule();
                        toastr.success('Item Successfully Added/Edited');
                        setTimeout(function () {
                            if (event_id) {
                                document.querySelector('#event_div_' + event_id).click();
                            }
                        }, 500);

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });
            },

            show_view_div(id) {

                let scope = this;
                scope.show_calendar = false;
                scope.show_file = false;
                scope.show_html = false;

                axios.get('/marketing/get_view_div_details', {
                    params: {
                        id: id
                    },
                })
                    .then(function (response) {

                        let file_type = response.data.uploads[0].file_type;
                        let file_url = response.data.uploads[0].file_url;
                        let html = response.data.uploads[0].html;
                        let subject_line_a = response.data.subject_line_a;
                        let subject_line_b = response.data.subject_line_b;
                        let preview_text = response.data.preview_text;

                        if (html) {

                            scope.show_html = true;
                            let details = ' \
                            <span class="font-semibold">Subject Line 1:</span> '+ subject_line_a + '<br> \
                            <span class="font-semibold">Subject Line 2:</span> '+ subject_line_b + '<br> \
                            <span class="font-semibold">Preview Text:</span> '+ preview_text;
                            scope.$refs.view_details_html.innerHTML = details;
                            let iframe = document.querySelector('.view-accepted-iframe');
                            iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe.contentDocument);
                            iframe.document.open();
                            iframe.document.write(html);
                            iframe.document.close();
                        } else {
                            scope.show_file = true;
                            console.log(file_url);
                            scope.$refs.view_file.setAttribute('src', file_url);
                            if (file_type == 'image') {
                                scope.$refs.view_file.setAttribute('height', 'auto');
                            } else if (file_type == 'pdf') {
                                scope.$refs.view_file.setAttribute('height', '100vh');
                            }
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            hide_view_div() {
                this.show_calendar = true;
                this.show_file = false;
                this.show_html = false;
                setTimeout(function () {
                    document.querySelector('.fc-dayGridMonth-button').click();
                }, 500);
            },

            edit_item(ele) {

                let scope = this;

                let event_div = ele.closest('.event-div');
                let id = event_div.getAttribute('data-id');
                scope.$refs.id.value = id;
                scope.$refs.event_date.value = event_div.getAttribute('data-event-date');

                let inputs = document.querySelectorAll('.states:checked');
                inputs.forEach(function (input) {
                    input.click();
                });

                let states = event_div.getAttribute('data-state').split(',');
                states.forEach(function (state) {
                    document.querySelector('#' + state).click();
                });
                scope.$refs.status_id.value = event_div.getAttribute('data-status-id');
                scope.$refs.recipient_id.value = event_div.getAttribute('data-recipient-id');
                scope.$refs.company_id.value = event_div.getAttribute('data-company-id');
                scope.$refs.medium_id.value = event_div.getAttribute('data-medium-id');
                scope.$refs.description.value = event_div.getAttribute('data-description');
                scope.$refs.subject_line_a.value = event_div.getAttribute('data-subject-line-a');
                scope.$refs.subject_line_b.value = event_div.getAttribute('data-subject-line-b');
                scope.$refs.preview_text.value = event_div.getAttribute('data-preview-text');
                scope.$refs.tracking_code.value = event_div.getAttribute('data-tracking-code');
                scope.$refs.goal_id.value = event_div.getAttribute('data-goal-id');
                scope.$refs.focus_id.value = event_div.getAttribute('data-focus-id');
                scope.show_email_options = false;
                if (scope.$refs.medium_id.options[scope.$refs.medium_id.selectedIndex].text == 'Email') {
                    scope.show_email_options = true;
                }
                scope.$refs.delete_event_button.setAttribute('data-id', event_div.getAttribute('data-id'));

                scope.$refs.show_versions_button.addEventListener('click', function () {
                    scope.show_versions(id);
                });

            },

            update_status(ele, event_id, status_id) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Updating ... ');

                let formData = new FormData();
                formData.append('event_id', event_id);
                formData.append('status_id', status_id);

                axios.post('/marketing/update_status', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        toastr.success('Status Successfully Updated');
                        scope.get_schedule();
                        setTimeout(function () {
                            document.querySelector('#event_div_' + event_id).click();
                            setTimeout(function () {
                                document.querySelector('.fc-dayGridMonth-button').click();
                            }, 100);
                        }, 300);
                    })
                    .catch(function (error) { });

            },

            show_delete_event(id, ele) {

                let scope = this;
                scope.show_delete_event_modal = true;

                scope.$refs.delete_event.addEventListener('click', function () {

                    let button_html = ele.innerHTML;
                    show_loading_button(ele, 'Deleting ... ');

                    let formData = new FormData();
                    formData.append('id', id);

                    axios.post('/marketing/delete_event', formData)
                        .then(function (response) {
                            ele.innerHTML = button_html;
                            scope.get_schedule();
                            scope.show_delete_event_modal = false;
                            scope.show_item_modal = false;
                            toastr.error('Event Successfully Deleted');
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
                        }, 500);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            add_version(id) {

                let scope = this;
                scope.clear_add_version_form();
                scope.show_add_version_modal = true;
                document.querySelector('#event_id').value = id;

            },

            delete_version(event_id, version_id) {

                let scope = this;
                let formData = new FormData();
                formData.append('version_id', version_id);

                axios.post('/marketing/delete_version', formData)
                    .then(function (response) {
                        scope.show_versions(event_id);
                        scope.get_schedule();
                        setTimeout(function () {
                            document.querySelector('#event_div_' + event_id).click();
                            document.querySelector('.fc-dayGridMonth-button').click();
                        }, 300);
                    })
                    .catch(function (error) { });
            },

            reactivate_version(event_id, version_id) {

                let scope = this;
                let formData = new FormData();
                formData.append('version_id', version_id);

                axios.post('/marketing/reactivate_version', formData)
                    .then(function (response) {
                        scope.show_versions(event_id);
                        scope.get_schedule();
                        setTimeout(function () {
                            document.querySelector('#event_div_' + event_id).click();
                            document.querySelector('.fc-dayGridMonth-button').click();
                        }, 300);
                    })
                    .catch(function (error) { });
            },

            mark_version_accepted(event_id, version_id) {

                let scope = this;
                let formData = new FormData();
                formData.append('event_id', event_id);
                formData.append('version_id', version_id);

                axios.post('/marketing/mark_version_accepted', formData)
                    .then(function (response) {
                        scope.show_versions(event_id);
                        scope.get_schedule();
                        setTimeout(function () {
                            document.querySelector('#event_div_' + event_id).click();
                            document.querySelector('.fc-dayGridMonth-button').click();
                        }, 1000);
                    })
                    .catch(function (error) { });

            },

            save_add_version(ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                let event_id = document.querySelector('#event_id').value;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.querySelector('#add_version_form');
                let formData = new FormData(form);

                this.clear_add_version_form();

                axios.post('/marketing/save_add_version', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.show_add_version_modal = false;
                        toastr.success('New Version Successfully Added');
                        scope.get_schedule();
                        scope.clear_add_version_form();
                        setTimeout(function () {
                            document.querySelector('#event_div_' + event_id).click();
                            document.querySelector('#view_' + event_id).click();
                            document.querySelector('.fc-dayGridMonth-button').click();
                        }, 300);

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });
            },

            clone(id) {

                let scope = this;

                let formData = new FormData();
                formData.append('id', id);

                axios.post('/marketing/clone_event', formData)
                    .then(function (response) {
                        let id = response.data.id;
                        scope.get_schedule(id);
                    })
                    .catch(function (error) { });

            },

            export_medium(id) {
                let scope = this;
                axios.get('/marketing/export_medium', {
                    params: {
                        id: id
                    },
                })
                    .then(function (response) {
                        if (response.data.html) {
                            let html = response.data.html;
                            if (html) {
                                scope.show_export_modal = true;
                                scope.$refs.html_textarea.value = html;
                            }
                        } else {
                            window.open(
                                response.data.file_url,
                                '_blank'
                            );
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            get_list(ele, type, count_div) {

                let event_div = ele.closest('.event-div');
                let states = event_div.getAttribute('data-state').split(',');
                let company_id = event_div.getAttribute('data-company-id');
                let recipient_id = event_div.getAttribute('data-recipient-id');
                let sender = 'sendinblue';
                if (company_id == '3' || company_id == '2') {
                    sender = 'mailchimp';
                }
                if (type == 'addresses') {
                    sender = '';
                }

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Getting List ... ');

                let formData = new FormData();
                formData.append('states', states);
                formData.append('sender', sender);
                formData.append('recipient_id', recipient_id);
                formData.append('type', type);

                axios.post('/marketing/get_list', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        count_div.classList.remove('hidden');
                        count_div.innerText = response.data.count;
                        window.location = response.data.location;
                        toastr.success('List Successfully Downloaded');
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

            },

            calendar() {

                let scope = this;
                let form = scope.$refs.filter_form;
                let formData = new FormData(form);

                axios.post('/marketing/calendar_get_events', formData)
                    .then(function (response) {

                        let calendarEl = document.querySelector('.calendar');
                        scope.calendar_object = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'showWeekends dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            events: response.data,
                            eventClick: function (info) {
                                let id = info.event.id;
                                document.querySelector('.edit-button[data-id="' + id + '"]').click();
                            },
                            weekends: false,
                            customButtons: {
                                showWeekends: {
                                    text: 'Show Weekends',
                                    click: function () {
                                        scope.show_weekends = !scope.show_weekends;
                                        scope.calendar_object.setOption('weekends', scope.show_weekends)
                                    }
                                }
                            },
                            height: 'auto',
                            fixedWeekCount: false,
                        });

                        scope.calendar_object.render();



                    })
                    .catch(function (error) {
                        console.log(error);
                    });



            },

            get_html_from_link(ele, textarea) {
                show_loading();
                setTimeout(function () {
                    textarea.value = '';
                    axios.get(ele.value)
                        .then(function (response) {
                            if (response.data) {
                                textarea.value = response.data;
                            } else {
                                toast.error('The URL is invalid');
                            }
                            hide_loading();
                        })
                        .catch(function (error) {
                            hide_loading();
                            toastr.error('URL Not Found');
                        });
                }, 1000);
            },

            update_states() {
                this.$refs.states.value = document.querySelectorAll('.states:checked').length;
            },

            show_email(ele, event_id) {

                let scope = this;
                let event_div = ele.closest('.event-div');

                scope.show_email_modal = true;

                if (event_div.getAttribute('data-subject-line-a') != '') {
                    scope.show_subject_options = true;
                    document.querySelector('[name="email_subject_line_a"]').value = event_div.getAttribute('data-subject-line-a');
                    document.querySelector('[name="email_subject"]').value = event_div.getAttribute('data-subject-line-a');
                    document.querySelector('[name="email_subject_line_b"]').value = event_div.getAttribute('data-subject-line-b');
                    document.querySelector('[name="email_preview_text"]').value = event_div.getAttribute('data-preview-text');
                } else {
                    scope.show_subject_options = false;
                    document.querySelector('[name="email_subject"]').value = event_div.getAttribute('data-description');
                }

                document.querySelector('[name="email_event_id"]').value = event_div.getAttribute('data-id');
                document.querySelector('[name="email_to"]').focus();

            },

            send_email(ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Sending Email ... ');
                remove_form_errors();

                let form = document.getElementById('email_form');
                let formData = new FormData(form);

                axios.post('/marketing/send_email', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.show_email_modal = false;
                        scope.clear_form(form);
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

            },

            update_to_addresses() {

                let scope = this;
                // let to_list = scope.$refs.to_list;
                let to_list = document.querySelector('.to-list');
                // let to_input = scope.$refs.email_to;
                let to_input = document.querySelector('[name="email_to"]');

                let all_addresses = [];
                to_list.querySelectorAll('.to-address').forEach(function (address) {
                    all_addresses.push(address.getAttribute('data-email'));
                });

                let to_addresses = [];
                to_list.querySelectorAll('.to-address:checked').forEach(function (address) {
                    to_addresses.push(address.getAttribute('data-email'));
                });

                let not_to_addresses = all_addresses.filter(x => to_addresses.indexOf(x) === -1);

                let input_addresses = to_input.value;
                if (input_addresses != '') {
                    if (input_addresses.match(/,/)) {
                        input_addresses = input_addresses.split(',');
                        input_addresses.forEach(function (address) {
                            address = address.trim();
                            if (address != '') {
                                if (!not_to_addresses.includes(address)) {
                                    to_addresses.push(address);
                                }
                            }
                        });
                    } else {
                        let address = input_addresses.trim();
                        if (!not_to_addresses.includes(address)) {
                            to_addresses.push(address);
                        }
                    }
                }

                to_addresses = [...new Set(to_addresses)];

                to_input.value = to_addresses.join(', ');

            },

            copy_text(ele) {
                ele.focus();
                ele.select();
                ele.setSelectionRange(0, 99999);
                copy_to_clipboard(ele)
                    .then(() =>
                        toastr.success('Link Successfully Copied To Clipboard'))
                    .catch(() => toastr.error('Link Not Copied To Clipboard'));
            },

            get_notes(event_id) {
                let scope = this;
                axios.get('/marketing/get_notes', {
                    params: {
                        event_id: event_id
                    },
                })
                    .then(function (response) {
                        document.querySelector('.notes-div[data-id="' + event_id + '"]').innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            add_notes(ele, event_id) {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let scope = this;
                let formData = new FormData();
                formData.append('event_id', event_id);
                formData.append('notes', tinymce.activeEditor.getContent());

                axios.post('/marketing/add_notes', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.get_notes(event_id);
                        tinymce.activeEditor.setContent('');
                        toastr.success('Note Saved');
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });
            },

            delete_note(ele, event_id, id) {
                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, '');
                remove_form_errors();

                let formData = new FormData();
                formData.append('id', id);

                axios.post('/marketing/delete_note', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.get_notes(event_id);
                        toastr.success('Note Deleted');
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });
            },

            mark_note_read(ele, event_id, note_id) {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');

                let formData = new FormData();
                formData.append('note_id', note_id);

                axios.post('/marketing/mark_note_read', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

                this.update_counter(document.querySelector('[data-note-id="' + event_id + '"]'));


            },

            update_counter(counter) {

                let count = parseInt(counter.innerText) - 1;
                counter.innerText = count;
                if (count == 0) {
                    counter.classList.add('hidden');
                } else {
                    counter.classList.remove('hidden');
                }

            },

            get_checklist(company_id, recipient_id, states) {

                let scope = this;
                axios.get('/marketing/get_checklist', {
                    params: {
                        company_id: company_id,
                        recipient_id: recipient_id,
                        states: states
                    },
                })
                    .then(function (response) {
                        scope.$refs.schedule_checklist_div.innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

        }

    }


}