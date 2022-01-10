if(document.URL.match(/view_lender/)) {


    window.lender = function(uuid) {

        return {

            save_details(ele) {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.querySelector('#details_form');
                let formData = new FormData(form);

                let orig_uuid = document.querySelector('[name="uuid"]').value;

                axios.post('/heritage_financial/lenders/save_details', formData)
                .then(function (response) {

                    ele.innerHTML = button_html;
                    toastr.success('Loan Details successfully saved');

                    setTimeout(function() {
                        if(orig_uuid == '') {
                            window.location = document.URL+'/'+response.data.uuid;
                        }
                    }, 500);

                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = button_html;
                            }
                        }
                    }
                });

            },
        }

    }

}
