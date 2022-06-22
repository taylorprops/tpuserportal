if (document.URL.match(/checklist/)) {

    window.checklist = function () {

        return {

            active_tab: '0',
            show_add_item_modal: false,
            modal_title: 'Add Item',

            init() {
                this.get_checklist();
                this.notes_editor(this.$refs.item);
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

                let scope = this;
                let state_select = scope.$refs.states;
                scope.show_add_item_modal = true;
                scope.$refs.company_id.value = company_id;
                states = states.split(',');
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

            notes_editor(ele) {

                let options = {
                    selector: ele,
                    height: 900,
                    width: 1000,
                    inline: true,
                    menubar: '',
                    statusbar: false,
                    plugins: 'image table code hr',
                    toolbar: 'undo redo | table | bold italic underline hr | forecolor backcolor | align outdent indent |  numlist bullist checklist | image | formatselect fontselect fontsizeselect | code |',
                    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'
                }
                text_editor(options);

            },

        }

    }

}