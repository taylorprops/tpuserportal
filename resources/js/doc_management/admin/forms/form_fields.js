

if(document.URL.match(/form_fields/)) {



    window.fill_fields = function() {

        return {

            selected_field_category: '',
            active_page: 1,
            options_side: '',
            active_field: '',

            init() {
                //this.get_fields();
            },

            show_selected_field_category(ele) {
                let buttons = document.querySelectorAll('.field-button');
                buttons.forEach(function(button) {
                    button.classList.remove('active', 'bg-secondary', 'border-secondary', 'ring-secondary', 'hover:bg-secondary-dark', 'active:border-secondary', 'focus:border-secondary');
                });
                ele.classList.add('active', 'bg-secondary', 'border-secondary', 'ring-secondary', 'hover:bg-secondary-dark', 'active:border-secondary', 'focus:border-secondary');
            },
            get_fields() {

                let scope = this;
                let container = document.querySelector('.forms-container');
                let form_id = container.getAttribute('data-form-id');

                axios.get('/doc_management/admin/forms/get_fields', {
                    params: {
                        form_id: form_id
                    },
                })
                .then(function (response) {

                    let data = response.data;

                    data.forEach(function(field, index) {

                        let field_html = document.querySelector('#field_template').innerHTML;

                        field_html = field_html.replace(/%%id%%/g, field.field_id);
                        field_html = field_html.replace(/%%category%%/g, field.field_category);
                        field_html = field_html.replace(/%%x_perc%%/g, field.left_perc);
                        field_html = field_html.replace(/%%y_perc%%/g, field.top_perc);
                        field_html = field_html.replace(/%%h_perc%%/g, field.height_perc);
                        field_html = field_html.replace(/%%w_perc%%/g, field.width_perc);

                        let page_container = document.querySelector('.page-'+field.page);

                        let div = document.createElement('div');
                        div.innerHTML = field_html;
                        page_container.appendChild(div);
                        unwrap(div);

                        let new_field = document.querySelector('[data-id="' + field.field_id + '"]');
                        new_field.classList.add('drag-resize');


                        new_field.setAttribute('data-is-group', field.is_group);
                        new_field.setAttribute('data-group-id', field.group_id);
                        new_field.setAttribute('data-page', field.page);
                        new_field.setAttribute('data-number-type', field.number_type);
                        new_field.setAttribute('data-common-field-id', field.common_field_id);
                        new_field.setAttribute('data-common-field-group-id', field.common_field_group_id);
                        new_field.setAttribute('data-common-field-sub-group-id', field.common_field_sub_group_id);
                        new_field.setAttribute('data-field-name', field.field_name);
                        new_field.setAttribute('data-db-column-name', field.db_column_name);
                        new_field.setAttribute('data-field-type', field.field_type);

                        // set common field name
                        new_field.querySelector('.common-field-input').value = field.field_name;
                        new_field.querySelector('.field-name').innerText = field.field_name;

                        if(field.field_category == 'radio') {
                            new_field.querySelector('.field-name').classList.add('rounded-full');
                            new_field.querySelector('.resizers').classList.add('rounded-full');
                        }

                        scope.coordinates(null, new_field, new_field.field_category);
                        scope.draggable(new_field, new_field.field_category);

                        setTimeout(function() {
                            if(document.querySelectorAll('[data-group-id="'+field.group_id+'"]').length > 1) {
                                document.querySelectorAll('[data-group-id="'+field.group_id+'"]').forEach(function(field) {
                                    /* field.__x.$data.is_group = true; */
                                });
                            }
                            scope.set_options_side(new_field);
                            scope.active_field = '';

                            if(index == data.length - 1) {
                                scope.resize();
                            }
                        }, 100);

                    });

                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            add_field(event) {
                if(this.selected_field_category != '') {
                    this.create_field(event);
                }
            },
            create_field(event) {

                let scope = this;
                let container = event.target.parentNode;
                let field_category = this.selected_field_category;
                let coords = this.coordinates(event, null, field_category);

                let field_html = document.querySelector('#field_template').innerHTML;
                let id = Date.now();

                field_html = field_html.replace(/%%id%%/g, id);
                field_html = field_html.replace(/%%category%%/g, field_category);
                field_html = field_html.replace(/%%x_perc%%/g, coords.x_perc);
                field_html = field_html.replace(/%%y_perc%%/g, coords.y_perc);
                field_html = field_html.replace(/%%h_perc%%/g, coords.h_perc);
                field_html = field_html.replace(/%%w_perc%%/g, coords.w_perc);

                let div = document.createElement('div');
                div.innerHTML = field_html;
                container.appendChild(div);
                unwrap(div);

                let new_field = document.querySelector('[data-id="' + id + '"]');
                new_field.classList.add('drag-resize');


                if(field_category == 'radio') {
                    new_field.querySelector('.field-name').classList.add('rounded-full');
                    new_field.querySelector('.resizers').classList.add('rounded-full');
                }

                this.coordinates(null, new_field, field_category);
                this.resize();

                this.active_field = id;
                console.log(Alpine.raw(this.active_field));
                setTimeout(function() {
                    scope.draggable(new_field, field_category);
                    scope.set_options_side(new_field);
                }, 1);


            },
            save_fields(ele) {

                show_loading_button(ele, 'Saving Fields...');

                let container = document.querySelector('.forms-container');
                let form_id = container.getAttribute('data-form-id');
                let fields_data = [];

                if(container.querySelector('.field-div')) {

                    let pages = container.querySelectorAll('.form-page-container');

                    pages.forEach(function(page) {

                        let field_page = page.getAttribute('data-page');
                        let fields = page.querySelectorAll('.field-div');

                        if(fields.length > 0) {

                            fields.forEach(function(field) {

                                let data = {
                                    'page': field_page,
                                    'id': field.getAttribute('data-id'),
                                    'group_id': field.getAttribute('data-group-id'),
                                    'is_group': field.getAttribute('data-is-group'),
                                    'category': field.getAttribute('data-category'),
                                    'common_field_id': field.getAttribute('data-common-field-id') || 0,
                                    'common_field_group_id': field.getAttribute('data-common-field-group-id') || 0,
                                    'common_field_sub_group_id': field.getAttribute('data-common-field-sub-group-id') || 0,
                                    'db_column_name': field.getAttribute('data-db-column-name') || '',
                                    'field_name': field.getAttribute('data-field-name') || '',
                                    'field_type': field.getAttribute('data-field-type') || '',
                                    'number_type': field.getAttribute('data-number-type'),
                                    'top_perc': field.getAttribute('data-y-perc'),
                                    'left_perc': field.getAttribute('data-x-perc'),
                                    'height_perc': field.getAttribute('data-h-perc'),
                                    'width_perc': field.getAttribute('data-w-perc'),
                                    'height_px': field.getAttribute('data-h-px'),
                                    'x': field.getAttribute('data-x'),
                                    'y': field.getAttribute('data-y'),
                                };

                                fields_data.push(data);

                            });

                        }

                    });

                }   else {

                    fields_data = null;

                }

                let formData = new FormData();
                formData.append('fields', JSON.stringify(fields_data));
                formData.append('form_id', form_id);

                axios.post('/doc_management/admin/forms/save_fields', formData)
                .then(function (response) {
                    toastr.success('Fields successfully saved', 'Success!');
                    ele.innerHTML = 'Save Fields <i class="fal fa-check ml-2"></i>';
                })
                .catch(function (error) {
                    if(error) {
                        alert(error)
                    }
                });

            },
            coordinates(event, ele = null, field_category) {

                console.log('running coordinates');

                let container, x, y;


                // if from dblclick to add field
                if (event) {

                    container = event.target.parentNode;

                    let page_boundaries = event.target.getBoundingClientRect();

                    // get target coordinates
                    // subtract bounding box coordinates from target coordinates to get top and left positions
                    // coordinates are relative to bounding box coordinates
                    x = parseInt(Math.round(event.clientX - page_boundaries.left));
                    y = parseInt(Math.round(event.clientY - page_boundaries.top));

                    // coordinates of existing field
                } else {

                    container = ele.parentNode;

                    x = ele.offsetLeft;
                    y = ele.offsetTop;

                }

                // convert to percent
                if(!container) {
                    //console.log('missing', ele);
                    return false;
                }
                let x_perc = this.pix_2_perc('x', x, container);
                let y_perc = this.pix_2_perc('y', y, container);


                //set heights
                let ele_h_perc = 1.3;
                if (field_category == 'radio' || field_category == 'checkbox') {
                    ele_h_perc = 1.1;
                }
                if (event) {
                    // remove element height from top position
                    y_perc = y_perc - ele_h_perc;
                }

                // set w and h for new field
                let h_perc, w_perc;
                if (ele) {
                    w_perc = (ele.offsetWidth / container.offsetWidth) * 100;
                    h_perc = (ele.offsetHeight / container.offsetHeight) * 100;
                } else {
                    if (field_category == 'radio' || field_category == 'checkbox') {
                        h_perc = 1.1;
                        w_perc = 1.45;
                    } else {
                        h_perc = 1.3;
                        w_perc = 15;
                    }
                }
                h_perc = parseFloat(h_perc);
                w_perc = parseFloat(w_perc);


                if (ele) {

                    let h_px = ele.offsetHeight;

                    // field data percents
                    ele.setAttribute('data-h-perc', h_perc);
                    ele.setAttribute('data-w-perc', w_perc);
                    ele.setAttribute('data-x-perc', x_perc);
                    ele.setAttribute('data-y-perc', y_perc);
                    ele.setAttribute('data-x', x);
                    ele.setAttribute('data-y', y);
                    ele.setAttribute('data-h-px', h_px);

                }

                return {
                    h_perc: h_perc,
                    w_perc: w_perc,
                    x_perc: x_perc,
                    y_perc: y_perc
                }

            },
            set_options_side(element) {

                let width = element.parentNode.offsetWidth;
                let left = element.offsetLeft;
                if(left > (width / 2)) {
                    this.options_side = 'right';
                } else {
                    this.options_side = 'left';
                }

            },
            copy_field(id, group) {

                let scope = this;
                let field = document.querySelector('[data-id="'+id+'"]');
                let group_id = field.getAttribute('data-group-id');
                let new_field = field.outerHTML;
                let field_category = field.getAttribute('data-category');
                let options_side = this.options_side;
                let field_top = field.offsetTop;
                let field_height = field.offsetHeight;
                let new_id = Date.now();
                let find_id = new RegExp(id, 'g');

                new_field = new_field.replace(find_id, new_id);
                let div = document.createElement('div');
                div.innerHTML = new_field;
                field.closest('.form-page-container').appendChild(div);
                unwrap(div);

                //field.closest('.form-page-container').innerHTML += new_field;

                setTimeout(function() {
                    new_field = document.querySelector('[data-id="'+new_id+'"]');
                    new_field.style.top = field_top + field_height + 10+'px';

                    if(group == false) {
                        new_field.querySelector('.common-field-input').value = '';
                        new_field.querySelector('.field-name').innerText = '';
                        new_field.setAttribute('data-group-id', new_id);
                    } else {
                        /* document.querySelector('[data-id="'+id+'"]').__x.$data.is_group = true;
                        new_field.__x.$data.is_group = true; */
                        new_field.setAttribute('data-group-id', group_id);
                        new_field.setAttribute('data-is-group', 'yes');
                    }

                    scope.resize();
                    scope.draggable(new_field, field_category);
                    scope.coordinates(null, new_field, field_category);
                    scope.options_side = options_side;
                    new_field.click();

                }, 300);


            },
            remove_field(id) {
                document.querySelector('[data-id="'+id+'"]').remove();
            },
            go_to_page(page) {
                document.querySelector('.page-header-'+page).scrollIntoView({behavior: 'smooth', block: 'start'});
                document.querySelector('.thumb-header-'+page).scrollIntoView({behavior: 'smooth', block: 'start'});
                active_page = page;
            },
            scroll_page(event) {

                // let page_container = event.target;
                // let pages = page_container.querySelectorAll('.form-page-container');
                // let cont = 'yes';

                // let c = 0;
                // pages.forEach(function(page) {
                //     if(c < 1) {
                //         c = 1;

                //         let start = Math.abs(page.getBoundingClientRect().top);
                //         let end = page.offsetHeight;
                //         let breakpoint = end * .75;
                //         console.log(start, breakpoint, end, page_container.scrollTop);


                //         if (start > breakpoint) {
                //             if(start > end) {
                //                 document.querySelector('.page-container').__x.$data.active_page = page;
                //                 console.log(page);
                //                 cont = 'no';
                //             }
                //         }
                //     }

                // });


                // let active_page = document.querySelector('.page-container').__x.$data.active_page;
                // let page_container = document.querySelector('.page-container');
                // let page = document.querySelector('.page-'+active_page);
                // let cont = 'yes';

                // let center = window.outerHeight / 2;
                // let start = page.getBoundingClientRect().top - page_container.getBoundingClientRect().top;
                // let end = start + page.offsetHeight;
                // console.log(start, center, end);


                // if (start < center && end > center) {

                //     document.querySelector('.page-container').__x.$data.active_page = page;
                //     console.log(page);
                //     cont = 'no';
                // }

            },
            resize() {

                //console.log('running resize');
                let scope = this;
                let field_divs = document.querySelectorAll('.field-div.drag-resize');

                field_divs.forEach(function(element) {

                    let resizers = element.querySelectorAll('.resizer');
                    let input_minimum_size = 15;
                    let check_radio_minimum_size = 10;
                    let check_radio_maximum_size = 30;
                    let original_width = 0;
                    let original_height = 0;
                    let original_x = 0;
                    let original_y = 0;
                    let original_mouse_x = 0;
                    let original_mouse_y = 0;
                    let keep_aspect = false;
                    let field_category = element.getAttribute('data-category');

                    //draggable(element, field_category);

                    for (let i = 0; i < resizers.length; i++) {
                        let currentResizer = resizers[i];
                        currentResizer.addEventListener('mousedown', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            original_width = parseFloat(element.offsetWidth);
                            original_height = parseFloat(element.offsetHeight);
                            original_x = element.offsetLeft;
                            original_y = element.offsetTop;
                            original_mouse_x = e.pageX;
                            original_mouse_y = e.pageY;
                            keep_aspect = field_category == 'radio' || field_category == 'checkbox' ? true : false;
                            window.addEventListener('mousemove', resize);
                            window.addEventListener('mouseup', stopResize);
                        })

                        function resize(e) {
                            if (currentResizer.classList.contains('bottom-right')) {
                                const width = original_width + (e.pageX - original_mouse_x);
                                const height = original_height + (e.pageY - original_mouse_y);
                                if(keep_aspect == true) {
                                    if (width > check_radio_minimum_size && width <= check_radio_maximum_size) {
                                        element.style.width = width + 'px';
                                        element.style.height = width + 'px';
                                    }
                                } else {
                                    if (width > input_minimum_size) {
                                        element.style.width = width + 'px';
                                    }
                                    if (height > input_minimum_size) {
                                        element.style.height = height + 'px';
                                    }

                                }
                            }
                            else if (currentResizer.classList.contains('bottom-left')) {
                                const height = original_height + (e.pageY - original_mouse_y);
                                const width = original_width - (e.pageX - original_mouse_x);
                                if(keep_aspect == true) {
                                    if (width > check_radio_minimum_size && width <= check_radio_maximum_size) {
                                        element.style.width = width + 'px';
                                        element.style.height = width + 'px';
                                        element.style.left = original_x + (e.pageX - original_mouse_x) + 'px';
                                    }
                                } else {
                                    if (height > input_minimum_size) {
                                        element.style.height = height + 'px';
                                    }
                                    if (width > input_minimum_size) {
                                        element.style.width = width + 'px';
                                        element.style.left = original_x + (e.pageX - original_mouse_x) + 'px';
                                    }
                                }
                            }
                            else if (currentResizer.classList.contains('top-right')) {
                                const width = original_width + (e.pageX - original_mouse_x);
                                const height = original_height - (e.pageY - original_mouse_y);
                                if(keep_aspect == true) {
                                    if (height > check_radio_minimum_size && height <= check_radio_maximum_size) {
                                        element.style.width = height + 'px';
                                        element.style.height = height + 'px';
                                        element.style.top = original_y + (e.pageY - original_mouse_y) + 'px';
                                    }
                                } else {
                                    if (width > input_minimum_size) {
                                        element.style.width = width + 'px';
                                    }
                                    if (height > input_minimum_size) {
                                        element.style.height = height + 'px';
                                        element.style.top = original_y + (e.pageY - original_mouse_y) + 'px';
                                    }
                                }
                            }
                            else {
                                const width = original_width - (e.pageX - original_mouse_x)
                                const height = original_height - (e.pageY - original_mouse_y)
                                if(keep_aspect == true) {
                                    if (height > check_radio_minimum_size && height <= check_radio_maximum_size) {
                                        element.style.width = height + 'px';
                                        element.style.height = height + 'px';
                                        element.style.left = original_x + (e.pageX - original_mouse_x) + 'px';
                                        element.style.top = original_y + (e.pageY - original_mouse_y) + 'px';
                                    }
                                } else {
                                    if (width > input_minimum_size) {
                                        element.style.width = width + 'px'
                                        element.style.left = original_x + (e.pageX - original_mouse_x) + 'px';
                                    }
                                    if (height > input_minimum_size) {
                                        element.style.height = height + 'px'
                                        element.style.top = original_y + (e.pageY - original_mouse_y) + 'px';
                                    }
                                }
                            }

                            scope.set_options_side(element);

                        }

                        function stopResize() {
                            window.removeEventListener('mousemove', resize);
                            scope.coordinates(null, element, field_category);
                            scope.draggable(element, field_category);
                        }
                    }

                });

            },
            draggable(element, field_category) {

                //console.log('running draggable');
                let scope = this;

                if(element.parentNode) {
                    let draggable = new PlainDraggable(element, {
                        handle: element.querySelector('.draggable-handle'),
                        //autoScroll: true,
                        //containment: element.parentNode,
                        leftTop: true,
                        onDrag: function(newPosition) {
                            scope.set_options_side(element);
                        },
                        onDragEnd: function(newPosition) {
                            scope.coordinates(null, element, field_category);
                        }
                    });
                }
            },
            pix_2_perc(type, px, container) {
                if (type == 'x') {
                    return (100 * parseFloat(px / parseFloat(container.offsetWidth)));
                } else {
                    return (100 * parseFloat(px / parseFloat(container.offsetHeight)));
                }
            },
            select_common_field(event) {

                let ele = event.currentTarget;
                let id = ele.getAttribute('data-id');
                let name = ele.getAttribute('data-name');
                let db_column_name = ele.getAttribute('data-db-column-name');
                let field_type = ele.getAttribute('data-field-type');
                let common_field_group_id = ele.getAttribute('data-common-field-group-id');
                let common_field_sub_group_id = ele.getAttribute('data-common-field-sub-group-id');

                let field_div = ele.closest('.field-div');

                field_div.querySelector('.common-field-input').value = name;
                field_div.querySelector('.field-name').innerText = name;

                field_div.setAttribute('data-common-field-id', id);
                field_div.setAttribute('data-common-field-group-id', common_field_group_id);
                field_div.setAttribute('data-common-field-sub-group-id', common_field_sub_group_id);
                field_div.setAttribute('data-field-name', name);
                field_div.setAttribute('data-db-column-name', db_column_name);
                field_div.setAttribute('data-field-type', field_type);

                this.active_field = '';

            },

        }
    }



}

