if(document.URL.match(/config_variables/)) {

    window.addEventListener('load', (event) => {

        get_config();

    });

    window.config = function(type) {

        return {
            config_add() {
                let form = document.getElementById('config_add_form');
                let formData = new FormData(form);
                axios.post('/resources/config/config_add', formData)
                .then(function (response) {
                    get_config();
                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            }
        }

    }

    window.get_config = function() {

        let cols = [
            { data: 'config_key' },
            { data: 'config_value', orderable: false },
            { data: 'value_type' }
        ];
        let table = document.querySelector('#config_table');

        data_table('/resources/config/get_config_variables', cols, 25, $(table), [0, 'asc'], [1], [], true, true, true, true, true);
    }

}
