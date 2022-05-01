if(document.URL.match('marketing/schedule_settings')) {

    window.schedule_settings = function() {

        return {

            show_delete_modal: false,

            init() {
                this.get_schedule_settings();
                this.text_editor();
            },

            get_schedule_settings() {

                let scope = this;

                axios.get('/marketing/get_schedule_settings')
                .then(function (response) {
                    scope.$refs.settings_div.innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });

            },

            settings_save_add_item(ele, type, input) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, '');

                let formData = new FormData();
                formData.append('type', type);
                formData.append('value', input.value);

                axios.post('/marketing/settings_save_add_item', formData)
                .then(function (response) {
                    ele.innerHTML = button_html;
                    scope.get_schedule_settings([type]);
                    input.value = '';
                    scope.show_add_item = false;
                })
                .catch(function (error) {
                });
            },

            settings_save_edit_item(id, value, field) {

                let formData = new FormData();
                formData.append('id', id);
                formData.append('value', value);
                formData.append('field', field);

                axios.post('/marketing/settings_save_edit_item', formData)
                .then(function (response) {
                    toastr.success('Item Successfully Changed');

                })
                .catch(function (error) {
                });
            },

            settings_show_delete_item(category, id) {

                let scope = this;
                axios.get('/marketing/settings_get_reassign_options', {
                    params: {
                        category: category,
                        id: id
                    },
                })
                .then(function (response) {
                    if(response.data.deleted) {
                        toastr.success('Item Successfully Deleted');
                        scope.get_schedule_settings([category]);
                        return;
                    }

                    scope.show_delete_modal = true;
                    let items_html = '';
                    response.data.settings.forEach(function(setting) {
                        items_html += ' \
                            <div class="p-2 border-b flex justify-between"> \
                                <div>'+setting.item+'</div> \
                        ';
                    });
                    scope.$refs.reassign_div.innerHTML = items;
                    scope.$refs.save_delete_item.addEventListener('click', function() {

                    });

                })
                .catch(function (error) {
                    console.log(error);
                });

            },

            settings_save_delete_item(id) {

            },

            text_editor() {

                let options = {
                    selector: '.editor-inline',
                    inline: true
                }
                text_editor(options);

            },



        }

    }

}
