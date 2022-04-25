if(document.URL.match('marketing/schedule')) {

    window.schedule = function() {

        return {

            show_add_item_modal:false,

            init() {
                this.get_schedule();
            },

            get_schedule() {
                let scope = this;
                axios.get('/marketing/get_schedule')
                .then(function (response) {
                    scope.$refs.schedule_list_div.innerHTML = response;
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            save_add_item(ele) {
                let scope = this;

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = scope.$refs.schedule_form;
                let formData = new FormData(form);

                axios.post('/marketing/save_add_item', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;

                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
            },

        }

    }


}
