if(document.URL.match('marketing/schedule_settings')) {

    window.schedule_settings = function() {

        return {

            show_delete_modal: false,

            init() {
                this.get_schedule_settings(['recipients', 'companies', 'mediums']);
                this.text_editor();
            },

            get_schedule_settings(types) {

                let scope = this;

                types.forEach(function(type) {

                    axios.get('/marketing/get_schedule_settings', {
                        params: {
                            type: type
                        },
                    })
                    .then(function (response) {
                        document.querySelector('[data-type="'+type+'"]').innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

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

            settings_save_edit_item(type, id, value) {

                let formData = new FormData();
                formData.append('type', type);
                formData.append('id', id);
                formData.append('value', value);

                axios.post('/marketing/settings_save_edit_item', formData)
                .then(function (response) {
                    toastr.success('Item Successfully Changed');

                })
                .catch(function (error) {
                });
            },

            settings_show_delete_item(type, id) {

                let scope = this;
                axios.get('/marketing/settings_get_reassign_options', {
                    params: {
                        type: type,
                        id: id
                    },
                })
                .then(function (response) {
                    if(response.data.deleted) {
                        toastr.success('Item Successfully Deleted');
                        scope.get_schedule_settings([type]);
                        return;
                    }

                    scope.show_delete_modal = true;
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
