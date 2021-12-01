if(document.URL.match(/profile/) || document.URL.match(/_view/)) {


    window.profile = function(emp_id, emp_type, photo_exists, text_editor_ele, url = null) {

        if(!emp_id) {
            photo_exists = false;
        }

        let profile = document.URL.match(/profile/) ? true : false;

        return {
            show_cropper_modal: false,
            cropper: '',
            employee_photo_pond: '',
            has_photo: photo_exists,
            show_ssn: false,
            show_email_error_modal: false,
            show_add_credit_card_modal: false,
            url: url,
            active_tab: '1',
            show_add_card_error_div: false,
            show_confirm_delete_credit_card: false,
            init() {

                if(emp_id) {
                    let show_licenses = ['agent', 'loan_officer'];
                    if(show_licenses.includes(emp_type)) {
                        this.get_licenses();
                    }

                    if(!profile) {
                        this.docs();
                        this.get_docs();
                    }
                    this.photo();
                    document.querySelectorAll('.filepond--credits').forEach(function(div) {
                        div.style.display = 'none';
                    });
                    if(text_editor_ele != '') {
                        this.init_text_editor(text_editor_ele);
                    }
                    this.show_profile_link();
                    this.show_floify_link();

                    if(emp_type == 'loan_officer') {
                        this.get_credit_cards();
                    }

                }
            },

            save_details(ele) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let scope = this;

                setTimeout(function() {

                    let form = document.getElementById('employee_form');
                    let formData = new FormData(form);
                    formData.append('emp_id', emp_id);
                    formData.append('emp_type', emp_type);

                    axios.post('/employees/save_details', formData)
                    .then(function (response) {
                        ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save';
                        if(response.data) {
                            if(response.data.success) {
                                toastr.success('Employee details successfully saved')
                            } else if(response.data.error) {
                                scope.show_email_error_modal = true;
                                document.querySelector('.error-message').innerHTML = response.data.error;
                            } else if(response.data.emp_id) {
                                emp_id = response.data.emp_id;
                                window.location = '/employees/'+emp_type+'/'+emp_type+'_view/'+emp_id;
                            }
                        }
                    })
                    .catch(function (error) {
                        if(error) {
                            if(error.response) {
                                if(error.response.status == 422) {
                                    let errors = error.response.data.errors;
                                    show_form_errors(errors);
                                    ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save';
                                }
                            }
                        }
                    });

                }, 500);
            },
            show_profile_link() {
                if(document.querySelector('#folder')) {
                    let folder = document.querySelector('#folder').value;
                    let link = this.url+'/'+folder;
                    document.querySelector('#folder_url').innerHTML = link;
                }
            },
            show_floify_link() {
                if(document.querySelector('#floify_folder')) {
                    let folder = document.querySelector('#floify_folder').value;
                    let link = 'https://'+folder+'.floify.com';
                    document.querySelector('#floify_folder_url').innerHTML = '<a href="'+link+'" target="_blank">'+link+'</a>';
                }
            },
            get_licenses() {
                axios.get('/employees/get_licenses', {
                    params: {
                        emp_id: emp_id,
                        emp_type: emp_type,
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
                                formData.append('emp_id', emp_id);
                                formData.append('emp_type', emp_type);
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
                formData.append('emp_id', emp_id);
                formData.append('emp_type', emp_type);
                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                axios.post('/employees/docs/get_docs', formData, axios_options)
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
                    formData.append('emp_id', emp_id);
                    formData.append('emp_type', emp_type);
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
                    formData.append('emp_id', emp_id);
                    formData.append('emp_type', emp_type);

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
                formData.append('emp_type', emp_type);
                formData.append('emp_id', emp_id);
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
            },
            save_signature(button) {
                show_loading_button(button, 'Saving Signature...');
                let signature = tinyMCE.activeEditor.getContent();
                signature = '<div style="width: 100%">'+signature+'</div>';

                let formData = new FormData();
                formData.append('signature', signature);
                formData.append('emp_type', emp_type);
                formData.append('emp_id', emp_id);
                axios.post('/employees/profile/save_signature', formData)
                .then(function (response) {
                    toastr.success('Your Signature has been saved successfully!');
                    button.innerHTML = '<i class="fal fa-check mr-2"></i> Save Signature';
                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            },

            get_credit_cards() {
                axios.get('/employees/billing/get_credit_cards', {
                    params: {
                        emp_type: emp_type,
                        emp_id: emp_id
                    },
                })
                .then(function (response) {
                    document.getElementById('credit_cards_div').innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            add_credit_card(ele) {

                let scope = this;

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();
                scope.show_add_card_error_div = false;

                let form = document.getElementById('add_credit_card_form');
                let formData = new FormData(form);
                formData.append('emp_id', emp_id);
                formData.append('emp_type', emp_type);

                axios.post('/employees/billing/add_credit_card', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save Credit Card';
                    if(response.data.success) {
                        scope.get_credit_cards();
                        scope.show_add_credit_card_modal = false;
                        //document.getElementById('add_credit_card_form').reset();
                        toastr.success('Credit Card successfully added');
                    } else if(response.data.error) {
                        scope.show_add_card_error_div = true;
                        document.getElementById('add_card_error_message').innerText = response.data.error;
                    }
                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save Credit Card';
                            }
                        }
                    }
                });
            },
            show_delete_credit_card(ele, profile_id, payment_profile_id) {

                let scope = this;

                let button = document.getElementById('confirm_delete_credit_card');
                button.setAttribute('data-profile-id', profile_id);
                button.setAttribute('data-payment-profile-id', payment_profile_id);
                button.setAttribute('data-ele', ele);

                scope.show_confirm_delete_credit_card = true;

            },
            delete_credit_card() {

                let scope = this;
                let button = document.getElementById('confirm_delete_credit_card');
                let ele = button.getAttribute('data-ele');
                ele = document.getElementById(ele);
                let profile_id  = button.getAttribute('data-profile-id');
                let payment_profile_id = button.getAttribute('data-payment-profile-id');

                ele.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

                let formData = new FormData();
                formData.append('profile_id', profile_id);
                formData.append('payment_profile_id', payment_profile_id);

                axios.post('/employees/billing/delete_credit_card', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-times"></i>';
                    if(response.data.success) {
                        scope.get_credit_cards();
                        toastr.success('Credit Card successfully deleted');
                        scope.show_confirm_delete_credit_card = false;
                    } else if(response.data.error) {

                    }
                })
                .catch(function (error) {
                });

            },
            set_default_credit_card(profile_id, payment_profile_id) {

                let scope = this;

                let formData = new FormData();
                formData.append('profile_id', profile_id);
                formData.append('payment_profile_id', payment_profile_id);

                axios.post('/employees/billing/set_default_credit_card', formData)
                .then(function (response) {
                    if(response.data.success) {
                        scope.get_credit_cards();
                        toastr.success('Default Credit Card Successfully Changed');
                    } else if(response.data.error) {

                    }
                })
                .catch(function (error) {
                });
            }

        }

    }



}
