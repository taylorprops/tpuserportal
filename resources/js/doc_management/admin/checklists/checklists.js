import Sortable from 'sortablejs/modular/sortable.complete.esm.js';

if (document.URL.match(/checklists$/)) {

    window.addEventListener('load', (event) => {



    });

    window.checklists = function() {

        return {

            location_id: '',
            active_type: 'listing',
            show_checklist_modal: false,
            locations: [],
            property_types: [],
            property_sub_types: [],
            show_property_sub_type: true,
            for_sale: '',
            checklist_modal_title: '',
            show_confirm_modal: false,
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
                        handle: ".handle",  // Drag handle selector within list items
                        draggable: ".checklist",  // Specifies which items inside the element should be draggable

                        chosenClass: "drag-clone",  // Class name for the chosen item
                        dragClass: "",  // Class name for the dragging item

                        swapThreshold: 1, // Threshold of the swap zone

                        dragoverBubble: false,

                        emptyInsertThreshold: 5, // px, distance mouse must be from empty sortable to insert drag element into it


                        setData: function (/** DataTransfer */dataTransfer, /** HTMLElement*/dragEl) {
                            dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
                        },

                        // Element is chosen
                        onChoose: function (/**Event*/evt) {
                            evt.oldIndex;  // element index within parent
                        },

                        // Element is unchosen
                        onUnchoose: function(/**Event*/evt) {
                            // same properties as onEnd
                        },

                        // Element dragging started
                        onStart: function (/**Event*/evt) {
                            evt.oldIndex;  // element index within parent
                        },

                        // Element dragging ended
                        onEnd: function (/**Event*/evt) {
                            var itemEl = evt.item;  // dragged HTMLElement
                            evt.to;    // target list
                            evt.from;  // previous list
                            evt.oldIndex;  // element's old index within old parent
                            evt.newIndex;  // element's new index within new parent
                            evt.oldDraggableIndex; // element's old index within old parent, only counting draggable elements
                            evt.newDraggableIndex; // element's new index within new parent, only counting draggable elements
                            evt.clone // the clone element
                            evt.pullMode;  // when item is in another sortable: `"clone"` if cloning, `true` if moving
                        },

                        // Element is dropped into the list from another list
                        onAdd: function (/**Event*/evt) {
                            // same properties as onEnd
                        },

                        // Changed sorting within list
                        onUpdate: function (/**Event*/evt) {
                            // same properties as onEnd
                        },

                        // Called by any change to the list (add / update / remove)
                        onSort: function (/**Event*/evt) {
                            // same properties as onEnd
                        },

                        // Element is removed from the list into another list
                        onRemove: function (/**Event*/evt) {
                            // same properties as onEnd
                        },

                        // Attempt to drag a filtered element
                        onFilter: function (/**Event*/evt) {
                            var itemEl = evt.item;  // HTMLElement receiving the `mousedown|tapstart` event.
                        },

                        // Event when you move an item in the list or between lists
                        onMove: function (/**Event*/evt, /**Event*/originalEvent) {
                            // Example: https://jsbin.com/nawahef/edit?js,output
                            evt.dragged; // dragged HTMLElement
                            evt.draggedRect; // DOMRect {left, top, right, bottom}
                            evt.related; // HTMLElement on which have guided
                            evt.relatedRect; // DOMRect
                            evt.willInsertAfter; // Boolean that is true if Sortable will insert drag element after target by default
                            originalEvent.clientY; // mouse position
                            // return false; — for cancel
                            // return -1; — insert before target
                            // return 1; — insert after target
                            // return true; — keep default insertion point based on the direction
                            // return void; — keep default insertion point based on the direction
                        },


                        onChange: function(evt) {
                            evt.newIndex // most likely why this event is used is to get the dragging element's current index
                            // same properties as onEnd
                        }
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
            add_items(checklist_id) {

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

            }

        }

    }

}

