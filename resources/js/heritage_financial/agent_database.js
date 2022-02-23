

window.agent_database = function() {

    return {

        show_add: false,

        init() {
            if(document.URL.match(/success/)) {
                toastr.success('List Successfully Added');
            }
        },

        add_list(ele) {

            if(this.$refs.agent_list.value != '') {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Adding List ... ');
                remove_form_errors();

                let form = document.getElementById('add_list_form');
                let formData = new FormData(form);

                axios.post('/heritage_financial/agent_database/add_new_list', formData)
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
