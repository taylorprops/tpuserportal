if(document.URL.match(/config_variables/)) {




    window.config = function(type) {

        return {
            init() {
                let scope = this;
                setTimeout(function() {
                    document.querySelectorAll('.config-input').forEach(function(input) {
                        input.addEventListener('change', (event) => {
                            scope.config_edit(event.target.getAttribute('data-id'), event.target.getAttribute('data-field'), event.target.value);
                        });
                    });
                }, 500);
            },
            config_add() {
                let form = document.getElementById('config_add_form');
                let formData = new FormData(form);
                axios.post('/resources/config/config_add', formData)
                .then(function (response) {
                    //get_config();
                    location.reload();
                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            },
            config_edit(id, field, value) {

                let formData = new FormData();
                formData.append('id', id);
                formData.append('field', field);
                formData.append('value', value);
                axios.post('/resources/config/config_edit', formData)
                .then(function (response) {
                    toastr.success('Config updated');
                })
                .catch(function (error) {

                });

            },

            config_delete(id, ele) {
                let formData = new FormData();
                formData.append('id', id);
                axios.post('/resources/config/config_delete', formData)
                .then(function (response) {
                    if(response.data.status === 'success') {
                        toastr.success('Config deleted');
                        ele.closest('tr').remove();
                    } else {
                        toastr.error('Not deleted!!');
                        console.log(response.data.results);
                    }
                })
                .catch(function (error) {

                });
            },

        }

    }


    /* window.get_config = function() {

        let cols = [
            { data: 'config_key' },
            { data: 'config_value', orderable: false },
            { data: 'value_type' }
        ];
        let table = document.querySelector('#config_table');
        // TODO: remove datatables
        data_table('/resources/config/get_config_variables', cols, 25, $(table), [0, 'asc'], [1], [], true, true, true, true, true);

        setTimeout(function() {
            document.querySelectorAll('.config-input').forEach(function(input) {
                input.addEventListener('change', (event) => {
                    config_edit(event.target.getAttribute('data-id'), event.target.getAttribute('data-field'), event.target.value);
                });
            });
        }, 500);
    } */

}
