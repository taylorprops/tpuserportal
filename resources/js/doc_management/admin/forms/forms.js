if (document.URL.match(/forms$/)) {

    window.addEventListener('load', (event) => {

        get_form_groups();
        setTimeout(get_forms, 500);

    });


    window.get_form_groups = function() {

        axios.get('/doc_management/admin/forms/get_form_groups')
        .then(function (response) {
            document.querySelector('#forms_div').innerHTML = response.data;
            // form_groups = document.querySelectorAll('[data-form-group-id]');
            // form_groups.forEach(function(form_group, index) {
            //     get_forms(form_group.getAttribute('data-form-group-id'));
            //     if(index === 0) {
            //         form_group.click();
            //         return false;
            //     }
            // });
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.get_forms = function(form_group_id = null) {

        if(!form_group_id) {
            form_group_id = document.querySelector('.page-container').__x.$data.active_tab;
        }
        let form_group = document.querySelector('.form-group-li.active');
        let sort_by = form_group.querySelector('.sort-by') ? form_group.querySelector('.sort-by').value : 'created_at';
        let published = form_group.querySelector('.show-published') ? form_group.querySelector('.show-published').value : 'all';
        let active = form_group.querySelector('.show-active') ? form_group.querySelector('.show-active').value : 'all';

        axios.get('/doc_management/admin/forms/get_forms', {
            params: {
                form_group_id: form_group_id,
                sort_by: sort_by,
                published: published,
                active: active
            },
        })
        .then(function (response) {
            document.querySelector('#form_group_'+form_group_id).innerHTML = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.get_upload_text = function (event) {

        if(event.target.value != '') {

            show_loader();

            let form = document.querySelector('#upload_form');
            let formData = new FormData(form);

            axios.post('/doc_management/admin/forms/get_upload_text', formData)
                .then(function (response) {

                    document.querySelector('.page-container').__x.$data.show_form_names = true;
                    document.querySelector('.form-names-div').classList.remove('hidden');

                    document.querySelector('#form_preview').innerHTML = '<embed src="' + response.data.upload_location + '#view=FitW" width="100%" height="100%">';

                    let form_names = document.querySelector('.form-names');

                    let form_name_template = document.getElementById('form_name_template').innerHTML;

                    form_names.innerHTML = '';
                    response.data.titles.forEach(function (title) {
                        let row = form_name_template.replace(/%%title%%/g, title);
                        form_names.innerHTML = form_names.innerHTML + row;
                    });

                    hide_loader();

                })
                .catch(function (error) {

                });

        }
    }

    window.add_form_name = function(container) {
        document.querySelector('.page-container').__x.$data.show_form_names = false;
        let title = container.querySelector('.form-name-title').value;
        document.querySelector('#form_name_display').value = title;
        document.querySelector('#helper_text').value = title;
    }

    window.edit_form = function(ele, form_id, form_name_display, form_location, form_group_id, checklist_group_id, form_tag, state, helper_text) {

        document.querySelector('.page-container').__x.$data.show_modal = true;
        document.querySelector('#current_form').innerHTML = '<span class="text-sm">Current form: <span class="font-bold">'+form_name_display+'</span></span>';
        document.querySelector('#form_id').value = form_id;
        document.querySelector('#form_name_display').value = form_name_display;
        document.querySelector('#form_group_id').value = form_group_id;
        document.querySelector('#checklist_group_id').value = checklist_group_id;
        document.querySelector('#form_tag').value = form_tag;
        document.querySelector('#state').value = state;
        document.querySelector('#helper_text').value = helper_text;
        document.querySelector('#form_preview').innerHTML = '<embed src="/storage/'+form_location+'#view=FitW" width="100%" height="100%">';

    }

    window.save_form = function(button) {

        let form = document.querySelector('#upload_form');
        let formData = new FormData(form);

        remove_form_errors();

        axios.post('/doc_management/admin/save_form', formData)
        .then(function (response) {

            if(response.data.error) {
                alert('There was an error adding the form, please try again');
            }

            if(document.querySelector('#form_id').value != '') {
                document.querySelector('.page-container').__x.$data.show_modal = false;
            }

            clear_form();

            button.innerHTML = decode_HTML(button.getAttribute('data-default-html'));
            button.removeAttribute('disabled');

            toastr.success('Form successfully saved', 'Success!');

            get_forms();

        })
        .catch(function (error) {
            if(error) {
                if(error.response) {
                    if(error.response.status == 422) {
                        let errors = error.response.data.errors;
                        show_form_errors(errors);
                        button.innerHTML = decode_HTML(button.getAttribute('data-default-html'));
                        button.removeAttribute('disabled');
                    }
                } else {
                    console.log(error);
                }
            }
        });

    }

    window.search_forms = function(ele) {

        let value = ele.value;
        if(value.length > 0) {
            axios.get('/doc_management/admin/forms/search_forms', {
                params: {
                    value: value
                },
            })
            .then(function (response) {
                let forms = JSON.parse(response.data.forms);
                let results = document.querySelector('#search_results');
                results.innerHTML = '';
                let search_results_li_template = document.getElementById('search_results_li_template').innerHTML;

                if(forms.length == 0) {

                    results.innerHTML = '<div class="p-4 text-lg text-center">No Results</div>';

                } else {

                    forms.forEach(function(form) {

                        let form_id = form.id;
                        let form_group_id = form.form_group_id;
                        let form_group = form.form_group.group_name;
                        let checklist_group_id = form.checklist_group_id;
                        let state = form.state;
                        let form_name_display = form.form_name_display;

                        let form_li = search_results_li_template.replace(/%%form_id%%/g, form_id);
                        form_li = form_li.replace(/%%form_group_id%%/g, form_group_id);
                        form_li = form_li.replace(/%%checklist_group_id%%/g, checklist_group_id);
                        form_li = form_li.replace(/%%state%%/g, state);
                        form_li = form_li.replace(/%%form_name_display%%/g, form_name_display);
                        form_li = form_li.replace(/%%form_group%%/g, form_group);

                        results.innerHTML += form_li;

                    });

                }

                document.querySelector('.page-container').__x.$data.show_search_results = true;

            })
            .catch(function (error) {
                console.log(error);
            });
        }

    }

    window.show_result = function(form_group_id, form_id) {

        document.querySelector('.form-group-'+form_group_id).click();

        setTimeout(function() {
            let list = document.querySelector('.form-group-li.active .form-ul');
            let form = document.querySelector('.form-'+form_id);
            list.insertBefore(form, list.childNodes[0]);
            list.__x.$data.active_form = form_id;
        }, 300);

    }

    window.clear_form = function() {
        let fields = document.querySelectorAll('#form_id, #upload, #form_name_display, #checklist_group_id, #helper_text');
        fields.forEach(function(field) {
            field.value = '';
        });
        document.querySelector('.form-names-div').classList.add('hidden');
        let divs = document.querySelectorAll('.file-names, #form_preview, #current_form');
        divs.forEach(function(div) {
            div.innerHTML = '';
        });
    }




}

