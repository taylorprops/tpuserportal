if(document.URL.match('marketing/schedule')) {

    window.schedule = function() {

        return {

            show_add_item_modal: false,
            show_html: false,
            show_file: false,

            init() {
                this.get_schedule();
            },

            get_schedule() {
                let scope = this;
                axios.get('/marketing/get_schedule')
                .then(function (response) {
                    scope.$refs.schedule_list_div.innerHTML = response.data;
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
                    toastr.success('Item Successfully Added');
                    scope.get_schedule();
                    scope.show_add_item_modal = false;
                })
                .catch(function (error) {
                    display_errors(error, ele, button_html);
                });
            },

            show_view_div(type, file, html) {
                let scope = this;
                if(html) {
                    scope.show_html = true;
                } else {
                    scope.show_file = true;
                    scope.$refs.view_file.setAttribute('src', file);
                    if(type == 'image') {
                        scope.$refs.view_file.setAttribute('height', 'auto');
                    } else if(type == 'pdf') {
                        scope.$refs.view_file.setAttribute('height', '100vh');
                    }
                }
            },

            show_edit_div(id) {

            },

        }

    }


}
