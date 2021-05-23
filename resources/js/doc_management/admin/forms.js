if (document.URL.match(/forms/)) {

    window.get_upload_text = function (event) {

        event.stopPropagation();

        if(document.querySelector('#upload').value != '') {

            let form = document.querySelector('#upload_form');
            let formData = new FormData(form);

            axios.post('/doc_management/admin/forms/get_upload_text', formData)
                .then(function (response) {

                    document.querySelector('#form_preview').innerHTML = '<embed src="' + response.data.upload_location + '#view=FitW" width="100%" height="100%">';

                    let form_names = document.querySelector('.form-names');

                    form_names.innerHTML = '<h5 class="text-secondary mb-2">Select and/or Edit Form Name</h5>';

                    response.data.titles.forEach(function (title) {
                        let row = ' \
                        <div class="flex justify-start align-center title-option w-100 mb-1"> \
                            <div class="w-20 flex-none"><button class="add-title rounded-md p-2 bg-gray-600 hover:bg-gray-700 mr-3 text-white">Select</button></div> \
                            <div class="flex-grow"><input type="text" class="rounded w-full" value="' + title + '" data-label=""></div> \
                        </div> \
                        ';
                        form_names.innerHTML = form_names.innerHTML + row;
                    });

                    // $('.add-title').on('click', function () {
                    //     $('.show-forms-button').show();
                    //     let title = $(this).closest('.title-option').find('input').val();
                    //     $('#file_name_display, #helper_text').val(title);
                    //     $('#file_name_display').on('change', function () {
                    //         if ($('#helper_text').val() == title) {
                    //             $('#helper_text').val($('#file_name_display').val());
                    //         }
                    //     });
                    //     $('#form_names_div').collapse('hide');
                    // });

                    // global_loading_off();

                })
                .catch(function (error) {

                });

        }
    }

    window.save_add_form = function() {

        alert('saved');

    }

}
