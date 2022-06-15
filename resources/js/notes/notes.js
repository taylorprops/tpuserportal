if (document.URL.match(/notes/)) {

    window.notes = function () {

        return {

            button_html: '',

            init() {

                let scope = this;

                scope.notes_editor('#notes');

                scope.button_html = scope.$refs.save_notes_button.innerHTML;

                setInterval(() => {
                    scope.save_notes();
                }, 3000);
            },

            save_notes(ele = null) {
                let scope = this;
                if (ele) {
                    show_loading_button(ele, 'Saving ... ');
                }

                let formData = new FormData();
                formData.append('notes', tinyMCE.activeEditor.getContent());

                axios.post('/notes/save_notes', formData)
                    .then(function (response) {
                        if (ele) {
                            toastr.success('Notes Successfully Saved');
                        } else {
                            let d = new Date();
                            let time = d.toLocaleTimeString();
                            scope.$refs.updated_at.innerText = time;
                        }
                        ele.innerHTML = scope.button_html;
                    })
                    .catch(function (error) {
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