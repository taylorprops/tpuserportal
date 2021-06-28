import Sortable from 'sortablejs/modular/sortable.complete.esm.js';

if (document.URL.match(/checklists$/)) {

    window.addEventListener('load', (event) => {

        sortable_items();

    });

    window.checklists = function() {

        return {

            location_id: '',
            active_type: 'listing',
            show_checklist_modal: false,
            show_add_items_modal: false,
            locations: [],
            property_types: [],
            property_sub_types: [],
            show_property_sub_type: true,
            for_sale: '',
            checklist_modal_title: '',
            show_confirm_modal: false,
            form_groups: [],
            active_form_group: '',
            searching_form_groups: false,
            active_edit_checklist_id: '',
            get_checklist_locations() {
                axios.get('/doc_management/admin/checklists/get_checklist_locations')
                .then(function (response) {
                    document.getElementById('checklist_locations').innerHTML = response.data;
                    document.querySelectorAll('.form-group')[0].click();
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            get_checklists() {
                let scope = this;
                axios.get('/doc_management/admin/checklists/get_checklists', {
                    params: {
                        location_id: scope.location_id
                    },
                })
                .then(function (response) {

                    document.getElementById('checklist_location_'+scope.location_id).innerHTML = response.data;

                    let sortable_div = document.querySelector('.checklist-sortable');
                    let sortable = Sortable.create(sortable_div, {
                        handle: ".checklist-handle",  // Drag handle selector within list items
                        draggable: ".checklist",  // Specifies which items inside the element should be draggable
                        chosenClass: "drag-clone",  // Class name for the chosen item

                        onEnd: function (evt) {

                            let ele = evt.item;  // dragged HTMLElement
                            let container = ele.closest('.checklist-sortable');
                            scope.update_order(container);

                        },

                    });
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            add_edit_checklist(ele, action, id, location_id, sale_rent, property_type_id, property_sub_type_id, checklist_type, represent) {
                //console.log(action, id, location_id, sale_rent, property_type_id, property_sub_type_id, checklist_type, represent);

                this.show_checklist_modal = true;
                this.checklist_modal_title = action == 'add' ? 'Add Checklist' : 'Edit Checklist';

                document.getElementById('id').value = id;
                document.getElementById('location_id').value = location_id;
                document.getElementById('sale_rent').value = sale_rent;
                document.getElementById('property_type_id').value = property_type_id;
                document.getElementById('property_sub_type_id').value = property_sub_type_id;
                document.getElementById('checklist_type').value = checklist_type;
                document.getElementById('represent').value = represent;

                if(checklist_type == '') {
                    checklist_type = this.active_type;
                }
                document.getElementById('checklist_type').value = checklist_type;
                if(represent == '') {
                    if(checklist_type == 'listing') {
                        represent = 'seller';
                    }
                }
                document.getElementById('represent').value = represent;

                if(sale_rent == 'rental' || document.getElementById('property_type_id').options[document.getElementById('property_type_id').selectedIndex].text != 'Residential') {
                    this.show_property_sub_type = false;
                    document.getElementById('property_sub_type_id').value = '';
                } else {
                    this.show_property_sub_type = true;
                }

            },
            save_checklist() {

                let scope = this;
                let form = document.querySelector('#checklist_form');
                let formData = new FormData(form);

                let property_sub_type_required = this.show_property_sub_type == true ? 'yes' : 'no';
                formData.append('property_sub_type_required', property_sub_type_required);
                let state = document.querySelector('#location_id').options[document.querySelector('#location_id').selectedIndex].getAttribute('data-state');
                formData.append('state', state);

                remove_form_errors();

                axios.post('/doc_management/admin/checklists/save_checklist', formData)
                .then(function (response) {
                    toastr.success('Checklist successfully saved!');
                    scope.show_checklist_modal = false;
                    scope.get_checklists();
                })
                .catch(function (error) {
                    if(error.response) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            },
            add_items(checklist_id, property_type, property_sub_type, checklist_type, sale_rent, represent, location) {

                this.active_edit_checklist_id = checklist_id;

                if(property_sub_type != '') {
                    property_type += ' : '+property_sub_type;
                }
                document.querySelector('.modal-title').innerHTML = 'Checklist Items | '+location+' | '+property_type+' | '+ucwords(checklist_type)+' | '+ucwords(sale_rent)+' | Rep: '+ucwords(represent);

                this.show_add_items_modal = true;

            },
            add_checklist_item(checklist_group_id, form_id, form_name) {

                let checklist_id = this.active_edit_checklist_id;
                let form_group_container = document.querySelector('.checklist-group[data-checklist-group-id="'+checklist_group_id+'"]');
                let name = Date.now();



                let form_html = document.getElementById('form_template').innerHTML;
                form_html = form_html.replace(/%%name%%/g, name);
                form_html = form_html.replace(/%%form_name%%/g, form_name);
                form_html = form_html.replace(/%%form_id%%/g, form_id);
                form_html = form_html.replace(/%%checklist_id%%/g, checklist_id);
                form_html = form_html.replace(/%%checklist_group_id%%/g, checklist_group_id);

                form_group_container.innerHTML += form_html;

                sortable_items();

                // let div = document.createElement('div');
                // div.innerHTML = form_html;
                // form_group_container.appendChild(div);
                // unwrap(div);

            },
            delete_checklist(checklist_id) {

                let scope = this;
                let formData = new FormData();

                scope.show_confirm_modal = true;

                document.querySelector('#confirm').addEventListener('click', function() {

                    formData.append('checklist_id', checklist_id);
                    axios.post('/doc_management/admin/checklists/delete_checklist', formData)
                    .then(function (response) {
                        toastr.success('Checklist deleted successfully');
                        scope.get_checklists();
                        scope.show_confirm_modal = false;
                    })
                    .catch(function (error) {
                        if(error) {
                            if(error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                            }
                        }
                    });

                });

            },
            update_order(container) {

                let checklists = [];
                container.querySelectorAll('.checklist').forEach(function(checklist, i) {
                    let data = {
                        id: checklist.getAttribute('data-checklist-id'),
                        order: i
                    }
                    checklists.push(data);
                });

                let formData = new FormData();
                formData.append('checklists', JSON.stringify(checklists));
                axios.post('/doc_management/admin/checklists/update_order', formData)
                .then(function (response) {
                    toastr.success('Reorder Successful');
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
            filter_checklists(container) {
                console.log(container);
                container.querySelectorAll('.checklist').forEach(function(checklist) {
                    console.log(checklist);
                    checklist.classList.toggle('hidden');
                    checklist.classList.toggle('flex');
                });

            },
            search_forms(val) {

                if(val.length > 0) {
                    let regex = new RegExp(val, 'gi');
                    document.querySelectorAll('.form-name').forEach(function(form) {
                        if(form.getAttribute('data-form-name').match(regex)) {
                            form.classList.remove('hidden');
                        } else {
                            form.classList.add('hidden');
                        }
                    });

                    this.searching_form_groups = true;

                } else {

                    this.searching_form_groups = false;
                    document.querySelectorAll('.form-name').forEach(function(form) {
                        form.classList.remove('hidden');
                    });
                }

            }

        }

    }

    window.sortable_items = function() {
        document.querySelectorAll('.checklist-group').forEach(function(sortable_div) {
            let sortable = Sortable.create(sortable_div, {
                handle: ".item-handle",
                draggable: ".form",
                chosenClass: "drag-clone",
            });
        });

    }

}

