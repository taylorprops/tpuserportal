
window.notes = function (note_type) {

    return {

        note_type: note_type,
        button_html: '',

        init() {

            let scope = this;

            scope.button_html = '';
            scope.get_notes();
            setInterval(() => {
                scope.save_notes();
            }, 3000);
        },

        get_notes() {
            let scope = this;
            axios.get('/notes/get_notes', {
                params: {
                    note_type: note_type
                },
            })
                .then(function (response) {
                    scope.$refs.notes.innerHTML = response.data;
                    setTimeout(function () {
                        scope.notes_editor('#notes_div');
                        scope.button_html = scope.$refs.save_notes_button.innerHTML;
                    }, 200);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        save_notes(ele = null) {
            let scope = this;
            if (ele) {
                show_loading_button(ele, 'Saving ... ');
            }

            let formData = new FormData();
            formData.append('notes', tinyMCE.activeEditor.getContent());
            formData.append('note_type', note_type)

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
                plugins: 'image table code hr autoresize paste',
                toolbar: 'undo redo | table | bold italic underline hr | forecolor backcolor | align outdent indent |  numlist bullist checklist | image | formatselect fontselect fontsizeselect | paste | code |',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                relative_urls: false,
                document_base_url: location.hostname,
            }
            text_editor(options);

        },

    }

}
