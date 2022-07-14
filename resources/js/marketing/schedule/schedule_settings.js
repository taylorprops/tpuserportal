
if (document.URL.match('marketing/schedule_settings')) {

    window.schedule_settings = function () {

        return {

            show_delete_modal: false,
            reassign_disabled: true,

            init() {

                let scope = this;

                scope.get_schedule_settings();
                scope.text_editor();

                scope.$refs.save_delete_item.addEventListener('click', function (e) {
                    scope.settings_save_delete_item(e);
                });
            },

            get_schedule_settings() {

                let scope = this;

                axios.get('/marketing/get_schedule_settings')
                    .then(function (response) {
                        scope.$refs.settings_div.innerHTML = response.data;
                        scope.sortable(scope.$refs.settings_div);
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

            settings_save_edit_item(id, value, field, ele = null) {

                let scope = this;
                let formData = new FormData();

                if (field == 'company_ids') {
                    value = [...ele.selectedOptions].map(o => o.value);
                }

                formData.append('id', id);
                formData.append('value', value);
                formData.append('field', field);

                axios.post('/marketing/settings_save_edit_item', formData)
                    .then(function (response) {
                        toastr.success('Item Successfully Changed');
                        scope.get_schedule_settings();

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
                        if (response.data.deleted) {
                            toastr.success('Item Successfully Deleted');
                            scope.get_schedule_settings([category]);
                            return;
                        }

                        scope.show_delete_modal = true;
                        let items_html = ' \
                    <div class="mb-6 flex items-center"> \
                        <div class="mr-6"><i class="fa-duotone fa-exclamation-circle fa-2x text-orange-400"></i></div> \
                        <div>The Item you are deleting is being used in saved events. To continue you will need to reassign a <span class="font-semibold">'+ response.data.settings[0].category.toUpperCase() + '</span> for those events.</div> \
                    </div> \
                    ';
                        response.data.settings.forEach(function (setting) {
                            items_html += ' \
                            <div class="p-2 border-b"> \
                                <div class="mr-3"><input type="radio" class="form-element radio lg" name="new_setting_id" value="'+ setting.id + '" data-label="' + setting.item + '" @click="reassign_disabled = false"></div> \
                            </div> \
                            <input type="hidden" name="deleted_setting_id" value="'+ id + '"> \
                            <input type="hidden" name="category" value="'+ category + '"> \
                        ';
                        });
                        scope.$refs.reassign_div.innerHTML = items_html;


                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            settings_save_delete_item(e) {

                let scope = this;
                let ele = e.target;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Reassigning and Deleting ... ');

                let form = scope.$refs.reassign_form;
                console.log(form);
                let formData = new FormData(form);

                axios.post('/marketing/settings_reassign_items', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.show_delete_modal = false;
                        scope.get_schedule_settings();
                        toastr.success('Item Successfully Deleted and Reassigned');
                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });
            },

            text_editor() {

                let options = {
                    selector: '.editor-inline',
                    inline: true
                }
                text_editor(options);

            },

            sortable(container) {

                let scope = this;

                container.querySelectorAll('.settings-options').forEach(function (sortable_div) {

                    let sortable = Sortable.create(sortable_div, {
                        handle: ".setting-handle",  // Drag handle selector within list items
                        draggable: ".settings-item",  // Specifies which items inside the element should be draggable
                        chosenClass: "sortable-chosen",  // Class name for the chosen item
                        ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                        dragClass: "sortable-drag",  // Class name for the dragging item

                        onEnd: function (evt) {

                            let ele = evt.item;
                            let container = ele.closest('.settings-options');
                            scope.settings_update_order(container);

                        },

                    });

                });

            },

            settings_update_order(container) {

                let settings = [];
                container.querySelectorAll('.settings-item').forEach(function (setting, i) {
                    let data = {
                        id: setting.getAttribute('data-id'),
                        order: i
                    }
                    settings.push(data);
                });

                let formData = new FormData();
                formData.append('settings', JSON.stringify(settings));
                axios.post('/marketing/settings_update_order', formData)
                    .then(function (response) {
                        toastr.success('Reorder Successful');
                    })
                    .catch(function (error) {
                    });

            }

        }

    }

}
