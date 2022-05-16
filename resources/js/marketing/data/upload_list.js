

window.upload_list = function() {

    return {

        list_type: 'in_house',

        init() {
            if(document.URL.match(/success/)) {
                toastr.success('List Successfully Added');
            }
        },

        add_list(ele) {

            let scope = this;

            if(this.$refs.upload_input.value != '') {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Adding List ... ');
                remove_form_errors();

                let form = document.getElementById('add_list_form');
                let formData = new FormData(form);
                formData.append('type', scope.list_type);

                axios.post('/marketing/data/add_new_list', formData)
                .then(function (response) {
                    window.location = document.URL.replace(/\?.*/, '')+'?status=success';
                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });

            }

        }

    }

}