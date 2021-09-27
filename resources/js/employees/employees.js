if(document.URL.match(/profile/) || document.URL.match(/loan_officer_view/)) {


    window.profile = function(employee_id, employee_type, photo_exists) {

        return {
            show_cropper_modal: false,
            cropper: '',
            employee_photo_pond: '',
            has_photo: photo_exists,
            init() {
                this.get_licenses();
                this.docs();
                this.get_docs();
                this.photo();
                document.querySelectorAll('.filepond--credits').forEach(function(div) {
                    div.style.display = 'none';
                });
                this.init_text_editor('#bio');
            },

            save_details(ele) {

                show_loading_button(ele, 'Saving ... ');

                setTimeout(function() {

                    let form = document.getElementById('employee_form');
                    let formData = new FormData(form);
                    formData.append('employee_id', employee_id);
                    formData.append('employee_type', employee_type);
                    // TODO: fix this
                    axios.post('/employees/save_details', formData)
                    .then(function (response) {
                        toastr.success('Employee details updated successfully');
                        ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save';
                    })
                    .catch(function (error) {
                        if(error) {
                            if(error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save';
                            }
                        }
                    });

                }, 500);
            },

            get_licenses() {
                axios.get('/employees/get_licenses', {
                    params: {
                        employee_id: employee_id,
                        employee_type: employee_type,
                    },
                })
                .then(function (response) {
                    document.querySelector('.licenses-div').innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            add_license() {
                let html = document.querySelector('#license_template').innerHTML;
                document.querySelector('.licenses-div').insertAdjacentHTML('beforeend', html);
            },
            delete_license(ele) {
                ele.closest('.license').remove();
            },

            docs() {

                let scope = this;
                let employee_docs = document.getElementById('employee_docs');
                scope.employee_docs_pond = FilePond.create(employee_docs);

                scope.employee_docs_pond.setOptions({
                    allowImagePreview: false,
                    multiple: true,
                    server: {
                        process: {
                            url: '/employees/docs/docs_upload',
                            onerror: (response) => response.data,
                            ondata: (formData) => {
                                formData.append('employee_id', employee_id);
                                formData.append('employee_type', employee_type);
                                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));
                                return formData;
                            }
                        }
                    },
                    labelIdle: 'Drag & Drop here or <span class="filepond--label-action"> Browse </span>',
                    onprocessfiles: () => {
                        scope.employee_docs_pond.removeFiles();
                        scope.get_docs();
                    }
                });
            },
            get_docs() {

                let scope = this;
                let formData = new FormData();
                formData.append('employee_id', employee_id);
                formData.append('employee_type', employee_type);
                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                axios.post('/employees/docs/get_docs', formData, axios_options)
                .then(function (response) {
                    document.querySelector('.docs-div').innerHTML = '';
                    response.data.docs.forEach(function(doc) {
                        let html = document.querySelector('#doc_template').innerHTML;
                        html = html.replace(/%%id%%/g, doc.id);
                        html = html.replace(/%%file_name%%/g, doc.file_name);
                        html = html.replace(/%%url%%/g, doc.file_location_url);
                        document.querySelector('.docs-div').insertAdjacentHTML('beforeend', html);
                    });
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
                    axios.post('/employees/docs/delete_doc', formData)
                    .then(function (response) {
                        scope.get_docs();
                        toastr.success('Document deleted successfully.');
                    })
                    .catch(function (error) {
                    });
                }
            },

            photo() {

                let scope = this;
                let employee_photo = document.getElementById('employee_photo');
                scope.employee_photo_pond = FilePond.create(employee_photo);

                scope.employee_photo_pond.setOptions({
                    allowImagePreview: false,
                    server: {
                        process: {
                            url: '/filepond_upload'
                        }
                    },
                    labelIdle: 'Drag & Drop here or<br><span class="filepond--label-action"> Browse </span>',
                    onpreparefile: (file, output) => {
                        let img = new Image();
                        img.src = URL.createObjectURL(output);
                        img.id = 'crop_image';
                        let width = img.naturalWidth;
                        let height = img.naturalHeight;

                        document.querySelector('.crop-container').innerHTML = '';
                        document.querySelector('.crop-container').appendChild(img);
                        //scope.show_cropper(width, height);
                        scope.show_cropper_modal = true;

                        let image = document.querySelector('#crop_image');
                        scope.cropper = new Cropper(image, {
                            aspectRatio: 3 / 4,
                            minContainerHeight: height,
                            minContainerWidth: width,
                            minCanvasHeight: height,
                            minCanvasWidth: width,

                        });

                    },
                    onprocessfile: (file, output) => {
                        //console.log(file, output);
                    }
                });
            },
            show_cropper(width, height) {

                this.show_cropper_modal = true;

                let image = document.querySelector('#crop_image');
                this.cropper = new Cropper(image, {
                    aspectRatio: 3 / 4,
                    minContainerHeight: height,
                    minContainerWidth: width,
                    minCanvasHeight: height,
                    minCanvasWidth: width,

                });

            },
            save_cropped_image(ele) {

                let scope = this;

                ele.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Saving Image...';

                scope.cropper.getCroppedCanvas({
                    width: 300,
                    height: 400,
                    fillColor: '#fff',
                    imageSmoothingEnabled: false,
                    imageSmoothingQuality: 'high',
                });

                // Upload cropped image to server if the browser supports `HTMLCanvasElement.toBlob`.
                // The default value for the second parameter of `toBlob` is 'image/png', change it if necessary.
                scope.cropper.getCroppedCanvas().toBlob((blob) => {

                    let formData = new FormData();

                    // Pass the image file name as the third parameter if necessary.
                    formData.append('cropped_image', blob);
                    formData.append('employee_id', employee_id);
                    formData.append('employee_type', employee_type);
                    formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                    axios.post('/employees/photos/save_cropped_upload', formData, axios_options)
                    .then(function (response) {
                        scope.cropper.destroy();
                        scope.show_cropper_modal = false;
                        document.getElementById('employee_image').setAttribute('src', response.data.url+'?t='+Date.now())
                        scope.has_photo = true;
                        scope.employee_photo_pond.removeFiles();
                        ele.innerHTML = '<i class="fad fa-save mr-2"></i> Save';
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }, 'image/png');

            },
            delete_photo() {

                if(confirm('Are you sure you want to delete this photo?')) {

                    let scope = this;
                    let formData = new FormData();
                    formData.append('employee_id', employee_id);
                    formData.append('employee_type', employee_type);

                    axios.post('/employees/photos/delete_photo', formData, axios_options)
                    .then(function (response) {
                        document.getElementById('employee_image').setAttribute('src', '');
                        scope.has_photo = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }
            },
            init_text_editor(ele) {

                let options = {
                    selector: ele,
                    height: 500,
                    //width: 700,
                    menubar: 'tools edit format table',
                    statusbar: false,
                    plugins: 'image table code',
                    toolbar: 'image | undo redo | styleselect | bold italic | forecolor backcolor | align outdent indent |',
                    table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                    relative_urls : false,
                    document_base_url: location.hostname
                }
                text_editor(options);

            },
            save_bio(button) {
                show_loading_button(button, 'Saving Bio...');
                let bio = tinyMCE.activeEditor.getContent();
                bio = '<div style="width: 100%">'+bio+'</div>';

                let formData = new FormData();
                formData.append('bio', bio);
                formData.append('employee_type', employee_type);
                formData.append('employee_id', employee_id);
                axios.post('/employees/profile/save_bio', formData)
                .then(function (response) {
                    toastr.success('Your Bio has been saved successfully!');
                    button.innerHTML = '<i class="fal fa-check mr-2"></i> Save Bio';
                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            }


        }

    }



}
