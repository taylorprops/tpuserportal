if(document.URL.match('address_database')) {

    window.address_database = function() {

        return {

            counties: [],
            counties_checked: [],
            results_time: '',
            list_group: 'agents',

            init() {
                this.location_data();
            },

            get_results() {

                let scope = this;
                ele_loading(document.querySelector('#results_div'));
                let time = Date.now();
                scope.results_time = time;

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

            },

            clear_results() {
                document.querySelector('#results_div').innerHTML = '';
            },

            clear_office_search_results() {
                document.querySelector('#office_search_results').innerHTML = '';
            },

            location_data() {

                let scope = this;
                let options_form = document.querySelector('#options_form');
                let formData = new FormData(options_form);
                scope.counties = [];

                axios.post('/marketing/data/location_data', formData)
                .then(function (response) {

                    scope.counties = response.data.counties;

                    setTimeout(function() {

                        scope.select_all_options('counties', true);
                        scope.$refs.select_all_counties.checked = true;
                        scope.search_offices();

                    }, 100);

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
                        let data = input.value.split('-');
                        let state = data[0];
                        let county = data[1];
                        let values = {
                            'state': state,
                            'county': county
                        }
                        counties.push(values);
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
                    this.clear_office_search_results();
                }
            },

            loan_officers_selected() {

                this.list_group = 'loan_officers';
                this.$refs.office_search.innerHTML = '';
                this.$refs.office_name.value = '';
                this.$refs.address_input.setAttribute('disabled', true);
                this.$refs.email_input.setAttribute('checked', true);
                this.$refs.address_input_div.classList.remove('opacity-100');
                this.$refs.address_input_div.classList.add('opacity-20');
                document.querySelectorAll('.disabled_loan_officer').forEach(function(state) {
                    state.classList.remove('opacity-100');
                    state.classList.add('opacity-20');
                    state.querySelector('input').setAttribute('disabled', true);
                    state.querySelector('input').checked = false;
                });
                this.update_details();

            },

            agents_selected() {

                this.list_group = 'agents_selected';
                this.$refs.address_input.removeAttribute('disabled');
                this.$refs.address_input_div.classList.remove('opacity-20');
                this.$refs.address_input_div.classList.add('opacity-100');

                document.querySelectorAll('.disabled_loan_officer').forEach(function(state) {
                    state.classList.remove('opacity-20');
                    state.classList.add('opacity-100');
                    state.querySelector('input').removeAttribute('disabled');
                });
                this.update_details();

            },

            update_details() {
                let county_checks = document.querySelectorAll('[name="counties[]"]');
                let county_checks_checked = document.querySelectorAll('[name="counties[]"]:checked');
                let state_checks = document.querySelectorAll('[name="states[]"]:not([disabled])');
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

                inputs = document.querySelectorAll('[name="'+elements+'[]"]:not([disabled])');
                inputs.forEach(function(input) {
                    input.checked = checked;
                });
                this.search_offices();
                this.update_details();

                if(elements == 'states') {
                    this.location_data();
                }
            },

            get_purged() {

                let start = this.$refs.purged_emails_start.value;
                let end = this.$refs.purged_emails_end.value;


                let formData = new FormData();
                formData.append('start', start);
                formData.append('end', end);

                axios.post('/marketing/data/get_purged', formData)
                .then(function (response) {
                    window.location = response.data.url;

                })
                .catch(function (error) {
                });

            }


        }

    }

}
