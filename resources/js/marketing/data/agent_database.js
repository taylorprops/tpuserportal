if(document.URL.match('agent_database')) {

    window.agent_database = function() {

        return {

            counties: [],
            counties_checked: [],
            results_time: '',

            init() {
                this.location_data('MD', true, true);
            },

            get_results() {

                let scope = this;
                ele_loading(document.querySelector('#results_div'));
                let time = Date.now();
                scope.results_time = time;

                setTimeout(function() {

                    let form = document.getElementById('options_form');
                    let formData = new FormData(form);

                    if(time == scope.results_time) {

                        axios.post('/marketing/data/get_results', formData)
                        .then(function (response) {
                            if(time >= scope.results_time) {
                                document.querySelector('#results_div').innerHTML = response.data;
                            }

                        })
                        .catch(function (error) {
                        });

                    }

                }, 800);

            },

            location_data(state, remove_current = true, on_init = null) {

                let scope = this;
                let options_form = document.querySelector('#options_form');
                let formData = new FormData(options_form);

                scope.get_checked(true);

                axios.post('/marketing/data/location_data', formData)
                .then(function (response) {

                    scope.counties = response.data.counties;

                    setTimeout(function() {
                        if(on_init) {
                            scope.select_all_options('counties', true);
                            scope.$refs.select_all_counties.checked = true;
                        }

                        if(state != '') {
                            document.querySelectorAll('[data-state="'+state+'"]').forEach(function(input) {
                                input.checked = true;
                            });
                        } else {
                            document.querySelectorAll('[name="counties[]"]').forEach(function(input) {
                                input.checked = true;
                            });
                        }

                        scope.search_offices();
                        //scope.get_results();

                    }, 100);

                    setTimeout(function() {
                        scope.add_checked();
                    }, 300);

                })
                .catch(function (error) {
                });

            },

            search_offices() {

                let scope = this;
                let val = document.querySelector('#office_search').value;
                let list_type = document.querySelector('[name="list_type"]:checked').value;
                if(val != '') {

                    let counties = [];
                    let inputs = document.querySelectorAll('[name="counties[]"]:checked');
                    inputs.forEach(function(input) {
                        let location_data = input.value.split('-');
                        let state = location_data[0];
                        let county = location_data[1];
                        let data = {
                            'state': state,
                            'county': county
                        }
                        counties.push(data);
                    });
                    counties = JSON.stringify(counties);

                    let formData = new FormData();
                    formData.append('val', val);
                    formData.append('counties', counties);
                    formData.append('list_type', list_type);

                    axios.post('/marketing/data/search_offices', formData)
                    .then(function (response) {
                        document.querySelector('#office_search_results').innerHTML = response.data;
                    })
                    .catch(function (error) {
                    });

                } else {
                    document.querySelector('#office_search_results').innerHTML = '';
                }
            },

            get_checked(remove_current) {
                let scope = this;
                scope.counties_checked = [];
                let checked = document.querySelectorAll('[name="counties[]"]:checked');
                if(checked.length > 0) {
                    checked.forEach(function(input) {
                        scope.counties_checked.push(input.value);
                    });
                }

                if(remove_current == true) {
                    document.querySelectorAll('.county-checkbox').forEach(function(input) {
                        input.remove();
                    });
                    scope.counties = [];
                }

            },

            add_checked() {
                let checked = Object.values(this.counties_checked);
                checked.forEach(function(input) {
                    if(document.querySelector('[value="' + input + '"]')) {
                        document.querySelector('[value="' + input + '"]').checked = true;
                    }
                });
                this.update_details();
            },

            update_details() {
                let county_checks = document.querySelectorAll('[name="counties[]"]');
                let county_checks_checked = document.querySelectorAll('[name="counties[]"]:checked');
                let state_checks = document.querySelectorAll('[name="states[]"]');
                let state_checks_checked = document.querySelectorAll('[name="states[]"]:checked');
                let state_count = document.querySelectorAll('[name="states[]"]:checked').length;
                let county_count = document.querySelectorAll('[name="counties[]"]:checked').length;

                if(county_checks.length === county_checks_checked.length) {
                    this.$refs.select_all_counties.checked = true;
                } else {
                    this.$refs.select_all_counties.checked = false;
                }

                if(state_checks.length === state_checks_checked.length) {
                    this.$refs.select_all_states.checked = true;
                } else {
                    this.$refs.select_all_states.checked = false;
                }

                document.querySelector('#state_count').innerText = state_count;
                document.querySelector('#county_count').innerText = county_count;
            },

            select_all_options(elements, checked) {
                inputs = document.querySelectorAll('[name="'+elements+'[]"]');
                inputs.forEach(function(input) {
                    input.checked = checked;
                });
                this.search_offices();
                this.get_results();
                this.update_details();

                if(elements == 'states') {
                    this.location_data('', false, false);
                }
            },


        }

    }

}
