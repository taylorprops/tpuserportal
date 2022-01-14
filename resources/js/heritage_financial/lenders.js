const { default: Toastr } = require("toastr2");


if(document.URL.match(/lenders/)) {

    window.lenders = function() {

        return {

            show_email_option: false,
            email_modal: false,
            signature: '',

            init() {
                this.message_editor('#message');
                this.get_signature();
            },

            check_all(checked) {
                document.querySelectorAll('.lender-checkbox').forEach(function(checkbox) {
                    checkbox.checked = checked;
                });
            },
            show_email_button() {
                if(document.querySelectorAll('.lender-checkbox:checked').length > 0) {
                    this.show_email_option = true;
                } else {
                    this.show_email_option = false;
                }
            },
            send_email(ele) {

                let scope = this;
                let lenders = [];
                document.querySelectorAll('.lender-checkbox:checked').forEach(function(lender) {
                    let details = {
                        'ae_name': lender.getAttribute('data-ae-name'),
                        'ae_email': lender.getAttribute('data-ae-email'),
                    }
                    lenders.push(details);
                });
                lenders = JSON.stringify(lenders);

                let subject = scope.$refs.subject.value;
                let message = tinymce.activeEditor.getContent();

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Sending Emails ... ');
                remove_form_errors();

                let formData = new FormData();
                formData.append('subject', subject);
                formData.append('message', message);
                formData.append('lenders', lenders);

                axios.post('/heritage_financial/lenders/email_lenders', formData)
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
                    toolbar: 'undo redo | table | bold italic underline | forecolor backcolor | align outdent indent |  numlist bullist checklist | image | formatselect fontselect fontsizeselect | code |',
                    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                    relative_urls : false,
                    document_base_url: location.hostname,
                    toolbar_location: 'bottom',
                    setup: function (editor) {
                        editor.on('init', function (e) {
                            editor.setContent('Hello %%AE_FirstName%%,<br><br><br><br>'+scope.signature);
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
            }
        }

    }



}
