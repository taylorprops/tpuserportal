if(document.URL.match(/view_lender/)) {


    window.lender = function(uuid) {

        return {

            uuid: uuid,
            lender_docs_pond: '',

            init() {
                this.textarea_height(this.$refs.notes);
                this.docs();
                this.get_docs();
            },

            save_details(ele) {

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.querySelector('#details_form');
                let formData = new FormData(form);

                let orig_uuid = document.querySelector('[name="uuid"]').value;

                axios.post('/heritage_financial/lenders/save_details', formData)
                .then(function (response) {

                    ele.innerHTML = button_html;
                    toastr.success('Loan Details successfully saved');

                    setTimeout(function() {
                        if(orig_uuid == '') {
                            window.location = document.URL+'/'+response.data.uuid;
                        }
                    }, 500);

                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = button_html;
                            }
                        }
                    }
                });

            },
            textarea_height(ele) {
                ele.style.height = "auto";
                ele.style.height = (ele.scrollHeight + 10) + "px";
            },

            docs() {

                let scope = this;
                let lender_docs = document.getElementById('lender_docs');
                scope.lender_docs_pond = FilePond.create(lender_docs);

                scope.lender_docs_pond.setOptions({
                    allowImagePreview: false,
                    multiple: true,
                    server: {
                        process: {
                            url: '/heritage_financial/lenders/docs_upload',
                            onerror: (response) => response.data,
                            ondata: (formData) => {
                                formData.append('uuid', scope.uuid);
                                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));
                                return formData;
                            }
                        }
                    },
                    labelIdle: 'Drag & Drop here or <span class="filepond--label-action"> Browse </span>',
                    onprocessfiles: () => {
                        scope.lender_docs_pond.removeFiles();
                        scope.get_docs();
                    }
                });
            },
            get_docs() {

                let scope = this;
                let formData = new FormData();
                formData.append('uuid', scope.uuid);
                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                axios.post('/heritage_financial/lenders/get_docs', formData, axios_options)
                .then(function (response) {
                    document.querySelector('.docs-div').innerHTML = '';
                    if(response.data.docs.length > 0) {
                        response.data.docs.forEach(function(doc) {
                            let html = document.querySelector('#doc_template').innerHTML;
                            html = html.replace(/%%id%%/g, doc.id);
                            html = html.replace(/%%file_name%%/g, doc.file_name);
                            html = html.replace(/%%url%%/g, doc.file_location_url);
                            document.querySelector('.docs-div').insertAdjacentHTML('beforeend', html);
                        });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            delete_doc(id) {
                if(confirm('Are you sure you want to delete this document?')) {
                    let scope = this;
                    let formData = new FormData();
                    formData.append('id', id);
                    axios.post('/heritage_financial/lenders/delete_doc', formData)
                    .then(function (response) {
                        scope.get_docs();
                        toastr.success('Document deleted successfully.');
                    })
                    .catch(function (error) {
                    });
                }
            },
        }

    }

}
