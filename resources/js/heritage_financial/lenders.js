

if(document.URL.match(/lenders/)) {

    window.lenders = function() {

        return {

            show_email_option: false,
            email_modal: false,

            init() {

            },

            check_all(checked) {
                document.querySelectorAll('.lender-checkbox').forEach(function(checkbox) {
                    checkbox.checked = checked;
                });
            },
            show_email() {
                if(document.querySelectorAll('.lender-checkbox:checked').length > 0) {
                    this.show_email_option = true;
                } else {
                    this.show_email_option = false;
                }
            },
        }

    }



}
