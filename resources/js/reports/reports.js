if(document.URL.match(/reports/)) {

    window.reports = function() {

        return {

            active_tab: 1,
            active_mortgage_tab: 1,
            show_print_all_option: false,

            init() {
                this.get_detailed_report(this.$refs.search_button);
            },

            get_detailed_report(ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Searching ... ');

                let form = scope.$refs.detailed_report_form;
                let formData = new FormData(form);
                formData.set('report_type', 'data');

                axios.post('/reports/mortgage/get_detailed_report', formData)
                .then(function (response) {
                    scope.$refs.results_div_data.innerHTML = response.data;
                })
                .catch(function (error) {
                });

                formData.set('report_type', 'details');
                axios.post('/reports/mortgage/get_detailed_report_details', formData)
                .then(function (response) {
                    scope.$refs.results_div_details.innerHTML = response.data;
                    ele.innerHTML = button_html;

                })
                .catch(function (error) {
                });

            },

            check_all(checked) {
                document.querySelectorAll('.report-checkbox').forEach(function(checkbox) {
                    checkbox.checked = checked;
                });
            },
            show_print_all_button() {
                if(document.querySelectorAll('.report-checkbox:checked').length > 0) {
                    this.show_print_all_option = true;
                } else {
                    this.show_print_all_option = false;
                }
            },

            print_report(button, reports = null) {

                let button_html = button.innerHTML;
                show_loading_button(button, 'Creating Report ... ');

                if(!reports) {
                    reports = [];
                    document.querySelectorAll('.report-checkbox:checked').forEach(function(report) {
                        reports.push(report.getAttribute('data-report'));
                    });
                } else {
                    reports = [reports];
                }

                axios.get('/reports/print', {
                    params: {
                        reports: reports
                    },
                })
                .then(function (response) {
                    button.innerHTML = button_html;
                    window.open('/storage/'+response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });

            }

        }

    }

}
