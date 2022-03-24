if(document.URL.match(/system_monitor/)) {

    window.monitor = function() {

        return {

            check_all: false,
            show_buttons_div: false,

            init() {
                this.get_failed_jobs();
            },

            get_failed_jobs() {

                let scope = this;

                axios.get('/admin/system_monitor/get_failed_jobs')
                .then(function (response) {
                    scope.$refs.failed_jobs_div.innerHTML = response.data;
                    setTimeout(function() {
                        let count = scope.$refs.failed_count.value;
                        scope.$refs.failed_count_view.innerHTML = count;
                    }, 500);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            show_buttons() {
                if(document.querySelectorAll('.job-checkbox:checked').length > 0) {
                    this.show_buttons_div = true;
                } else {
                    this.show_buttons_div = false;
                }
            },

            delete_checked(ele) {

                let scope = this;
                let inputs = document.querySelectorAll('.job-checkbox:checked');
                let checked = [];
                inputs.forEach(function(input) {
                    checked.push(input.getAttribute('data-id'));
                });

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Deleting ... ');

                let formData = new FormData();
                formData.append('checked', checked);

                axios.post('/admin/system_monitor/delete_failed_jobs', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;
                    scope.get_failed_jobs();

                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
            },

        }

    }

}
