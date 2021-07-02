if (document.URL.match(/forms$/)) {

    window.forms = function(active_tab) {

        return {
            active_tab: active_tab,
            show_modal: false,
            show_search_results: false,
            show_form_names: true,
            init() {

                this.get_form_groups();

            },
            get_form_groups() {

                let scope = this;
                axios.get('/doc_management/admin/forms/get_form_groups')
                .then(function (response) {
                    document.querySelector('#forms_div').innerHTML = response.data;
                    scope.get_forms();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            get_forms() {

                let scope = this;
                let form_group = document.querySelector('.form-group-li.active');
                let sort_by = form_group.querySelector('.sort-by') ? form_group.querySelector('.sort-by').value : 'created_at';
                let published = form_group.querySelector('.show-published') ? form_group.querySelector('.show-published').value : 'all';
                let active = form_group.querySelector('.show-active') ? form_group.querySelector('.show-active').value : 'all';


                axios.get('/doc_management/admin/forms/get_forms', {
                    params: {
                        form_group_id: scope.active_tab,
                        sort_by: sort_by,
                        published: published,
                        active: active
                    },
                })
                .then(function (response) {
                    document.querySelector('#form_group_'+scope.active_tab).innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            search_forms(ele) {

                let scope = this;
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

                                results.insertAdjacentHTML('beforeend', form_li);

                            });

                        }

                        scope.show_search_results = true;

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }

            },
            show_result(form_group_id, form_id) {

                document.querySelector('.form-group-'+form_group_id).click();

                setTimeout(function() {
                    let list = document.querySelector('.form-group-li.active .form-ul');
                    let form = document.querySelector('.form-'+form_id);
                    list.insertBefore(form, list.childNodes[0]);
                    this.active_form = form_id;
                }, 300);

            },
            clear_form() {

                let fields = document.querySelectorAll('#form_id, #upload, #form_name_display, #checklist_group_id, #helper_text');
                fields.forEach(function(field) {
                    field.value = '';
                });
                document.querySelector('.form-names-div').classList.add('hidden');
                let divs = document.querySelectorAll('.file-names, #form_preview, #current_form');
                divs.forEach(function(div) {
                    div.innerHTML = '';
                });

            },
            get_upload_text(event) {

                if(event.target.value != '') {

                    show_loading();

                    let scope = this;
                    let form = document.querySelector('#upload_form');
                    let formData = new FormData(form);

                    axios.post('/doc_management/admin/forms/get_upload_text', formData)
                        .then(function (response) {

                            scope.show_form_names = true;
                            document.querySelector('.form-names-div').classList.remove('hidden');

                            document.querySelector('#form_preview').innerHTML = '<embed src="' + response.data.upload_location + '#view=FitW" width="100%" height="100%">';

                            let form_names = document.querySelector('.form-names');

                            let form_name_template = document.getElementById('form_name_template').innerHTML;

                            form_names.innerHTML = '';
                            response.data.titles.forEach(function (title) {
                                let row = form_name_template.replace(/%%title%%/g, title);
                                form_names.innerHTML = form_names.innerHTML + row;
                            });

                            hide_loading();

                        })
                        .catch(function (error) {

                        });

                }
            },
            add_form_name(container) {
                this.show_form_names = false;
                let title = container.querySelector('.form-name-title').value;
                document.querySelector('#form_name_display').value = title;
                document.querySelector('#helper_text').value = title;
            },
            save_form(button) {

                let scope = this;
                let form = document.querySelector('#upload_form');
                let formData = new FormData(form);

                remove_form_errors();

                axios.post('/doc_management/admin/save_form', formData)
                .then(function (response) {

                    if(response.data.error) {
                        alert('There was an error adding the form, please try again');
                    }

                    if(document.querySelector('#form_id').value != '') {
                        scope.show_modal = false;
                    }

                    scope.clear_form();

                    button.innerHTML = decode_HTML(button.getAttribute('data-default-html'));
                    button.removeAttribute('disabled');

                    toastr.success('Form successfully saved', 'Success!');

                    scope.get_forms();

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

            },
            edit_form(ele, form_id, form_name_display, form_location, form_group_id, checklist_group_id, form_tag, state, helper_text) {

                this.show_modal = true;
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

        }
    }

}

