if(document.URL.match(/profile/)) {


    window.loan_officer = function(loan_officer_id, photo_exists) {

        return {
            show_cropper_modal: false,
            cropper: '',
            loan_officer_photo_pond: '',
            has_photo: photo_exists,
            init() {
                this.photo();
                document.querySelectorAll('.filepond--credits').forEach(function(div) {
                    div.style.display = 'none';
                });
                this.init_text_editor();
            },
            photo() {

                let scope = this;
                let loan_officer_photo = document.getElementById('loan_officer_photo');
                scope.loan_officer_photo_pond = FilePond.create(loan_officer_photo);

                scope.loan_officer_photo_pond.setOptions({
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


                        // $('#crop_modal').on('hidden.bs.modal', function(){
                        //     scope.loan_officer_photo_pond.removeFiles();
                        // });

                        let image = document.querySelector('#crop_image');
                        scope.cropper = new Cropper(image, {
                            aspectRatio: 3 / 4,
                            minContainerHeight: height,
                            minContainerWidth: width,
                            minCanvasHeight: height,
                            minCanvasWidth: width,

                        });



                        // $('#crop_modal').on('hidden.bs.modal', function() {
                        //     scope.loan_officer_photo_pond.removeFiles();
                        // });
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
                    formData.append('loan_officer_id', loan_officer_id);
                    formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                    axios.post('/employees/loan_officers/photos/save_cropped_upload_loan_officer', formData, axios_options)
                    .then(function (response) {
                        scope.cropper.destroy();
                        scope.show_cropper_modal = false;
                        document.getElementById('loan_officer_image').setAttribute('src', response.data.url+'?t='+Date.now())
                        scope.has_photo = true;
                        scope.loan_officer_photo_pond.removeFiles();
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
                    formData.append('loan_officer_id', loan_officer_id);

                    axios.post('/employees/loan_officers/photos/delete_photo_loan_officer', formData, axios_options)
                    .then(function (response) {
                        document.getElementById('loan_officer_image').setAttribute('src', '');
                        scope.has_photo = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }
            },
            init_text_editor() {

                let options = {
                    selector: '#bio',
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
                axios.post('/heritage_financial/loan_officers/profile/save_bio', formData)
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
