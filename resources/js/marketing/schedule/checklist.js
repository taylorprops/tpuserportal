if (document.URL.match(/checklist/)) {

    window.checklist = function () {

        return {

            active_tab: '0',
            show_add_item_modal: false,
            modal_title: 'Add Item',

            init() {
                this.get_checklist();
                this.notes_editor();
            },

            get_checklist() {
                let scope = this;
                axios.get('/marketing/schedule/get_checklist')
                    .then(function (response) {
                        scope.$refs.checklist_div.innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            add_item(company_id, recipient_id, selected_state, states) {

                console.log(states);
                let scope = this;
                let state_select = scope.$refs.states;
                scope.show_add_item_modal = true;
                scope.$refs.company_id.value = company_id;
                states = states.split(',');
                state_select.innerHTML = '';
                states.forEach(function (state) {

                    let option = document.createElement('option');
                    option.value = state;
                    option.text = state;
                    if (state == selected_state) {
                        option.selected = true;
                    }
                    state_select.append(option);

                });

            },

            save_item(ele) {

                let scope = this;
                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');

                let form = scope.$refs.add_item_form;
                let formData = new FormData(form);
                formData.append('data', tinymce.activeEditor.getContent());

                axios.post('/marketing/schedule/save_item', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

            },

            notes_editor() {

                let options = {
                    selector: '#data',
                    height: 400,
                    menubar: '',
                    statusbar: false,
                    plugins: 'image table code hr autoresize',
                    toolbar: 'undo redo | table | bold italic underline hr | forecolor backcolor | align outdent indent |  numlist bullist checklist | image | formatselect fontselect fontsizeselect | code |',
                    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                    relative_urls: false,
                    document_base_url: location.hostname,
                }
                text_editor(options);

            },

        }

    }

}