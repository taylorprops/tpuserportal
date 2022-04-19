if(document.URL.match('marketing/schedule_settings')) {

    window.schedule_settings = function() {

        return {


            init() {
                this.get_schedule_settings(['categories', 'mediums']);
            },

            get_schedule_settings(fields) {

                let scope = this;

                fields.forEach(function(field) {

                    axios.get('/marketing/get_schedule_settings', {
                        params: {
                            field: field
                        },
                    })
                    .then(function (response) {
                        document.querySelector('[data-field="'+field+'"]').innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                });

            }
        }

    }

}
