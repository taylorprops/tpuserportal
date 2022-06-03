if (document.URL.match(/notes/)) {

    window.notes = function () {

        return {

            init() {
                this.notes_editor('#notes');
            },

            save_notes(ele) {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let formData = new FormData();
                formData.append('notes', tinyMCE.activeEditor.getContent());

                axios.post('/notes/save_notes', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
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
                    plugins: 'image table code',
                    toolbar: 'undo redo | table | bold italic underline | forecolor backcolor | align outdent indent |  numlist bullist checklist | image | formatselect fontselect fontsizeselect | code |',
                    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'
                }
                text_editor(options);

            },

        }

    }

}