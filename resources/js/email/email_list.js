window.email_list = function () {

    return {

        show_email_option: false,
        email_modal: false,
        signature: '',

        init() {
            let scope = this;
            scope.get_signature();
            setTimeout(function () {
                scope.message_editor('#message');
            }, 500);
        },

        check_all(checked) {
            document.querySelectorAll('.recipient-checkbox').forEach(function (checkbox) {
                checkbox.checked = checked;
            });
        },
        show_email_button() {
            if (document.querySelectorAll('.recipient-checkbox:checked').length > 0) {
                this.show_email_option = true;
            } else {
                this.show_email_option = false;
            }
        },
        show_recipients_added() {
            let div = this.$refs.recipients_added;
            div.innerHTML = '';
            document.querySelectorAll('.recipient-checkbox:checked').forEach(function (recipient) {
                let name = recipient.getAttribute('data-name');
                let email = recipient.getAttribute('data-email');
                let company = recipient.getAttribute('data-company') || '';
                let html = '<div class="text-xs grid grid-cols-8 my-1 border-b"> \
                    <div class="col-span-2">'+ name + '</div> \
                    <div class="col-span-3">'+ email + '</div> \
                    <div class="col-span-3">'+ company + '</div> \
                </div>';
                div.insertAdjacentHTML('beforeend', html);
            });
        },
        send_email(ele, company) {

            let scope = this;
            let recipients = [];
            document.querySelectorAll('.recipient-checkbox:checked').forEach(function (recipient) {
                let details = {
                    'name': recipient.getAttribute('data-name'),
                    'email': recipient.getAttribute('data-email'),
                }
                recipients.push(details);
            });
            recipients = JSON.stringify(recipients);

            let message = tinymce.activeEditor.getContent();

            let button_html = ele.innerHTML;
            show_loading_button(ele, 'Sending Emails ... ');
            remove_form_errors();

            let form = document.querySelector('#email_list_form');
            let formData = new FormData(form);
            formData.append('recipients', recipients);
            formData.append('message', message);
            formData.append('company', company);

            axios.post('/email/email_list', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;
                    scope.email_modal = false;
                    toastr.success('Emails Successfully Sent');
                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
        },

        message_editor(ele) {

            let scope = this;

            let options = {
                selector: ele,
                height: 300,
                menubar: '',
                statusbar: false,
                plugins: 'image table code',
                toolbar: 'undo redo | table | bold italic underline | forecolor backcolor | align outdent indent |  numlist bullist checklist | image |     formatselect fontselect fontsizeselect | code |',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                relative_urls: false,
                document_base_url: location.hostname,
                toolbar_location: 'bottom',
                setup: function (editor) {
                    editor.on('init', function (e) {
                        editor.setContent('Hello %%FirstName%%,<br><br><br><br>' + scope.signature);
                    });
                }
            }
            text_editor(options);

        },
        get_signature() {
            let scope = this;
            axios.get('/global/get_signature')
                .then(function (response) {
                    scope.signature = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

    }

}
