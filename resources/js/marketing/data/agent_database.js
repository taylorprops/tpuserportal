if(document.URL.match('agent_database')) {

    window.agent_database = function() {

        return {

            counties: [],
            //cities: [],

            init() {
                this.location_data(true);
            },

            location_data(on_init = null) {

                let scope = this;
                let search_form = document.querySelector('#search_form');
                let formData = new FormData(search_form);

                axios.post('/marketing/data/location_data', formData)
                .then(function (response) {
                    //scope.cities = response.data.cities;
                    scope.counties = response.data.counties;

                    setTimeout(function() {
                        if(on_init) {
                            scope.select_all_options(document.querySelector('#counties'));
                        }
                        //scope.select_all_options(document.querySelector('#cities'));

                        let state_count = Array.from(document.querySelector('#states').selectedOptions).length;
                        let county_count = Array.from(document.querySelector('#counties').selectedOptions).length;
                        //let city_count = Array.from(document.querySelector('#cities').selectedOptions).length;
                        document.querySelector('#state_count').innerText = state_count;
                        document.querySelector('#county_count').innerText = county_count;
                        //document.querySelector('#city_count').innerText = city_count;
                    }, 200);
                })
                .catch(function (error) {
                });

            },

            search_offices(val) {

                let scope = this;
                if(val != '') {

                    axios.get('/marketing/data/search_offices', {
                        params: {
                            val: val,
                            counties: scope.counties
                        },
                    })
                    .then(function (response) {
                        document.querySelector('#office_search_results').innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                } else {
                    document.querySelector('#office_search_results').innerHTML = '';
                }
            },

            select_all_options(ele) {
                for (var i = 0; i < ele.options.length; i++) {
                    ele.options[i].selected = true;
                }
            },

        }

    }

}
