

if(document.URL.match(/form_fields/)) {

    window.addEventListener('load', (event) => {



    });


    window.fill_fields = function() {
        return {
            selected_field_type: '',
            active_page: 1,
            options_side: '',
            active_field: '',
            show_selected_field_type(ele) {
                let buttons = document.querySelectorAll('.field-button');
                buttons.forEach(function(button) {
                    button.classList.remove('active', 'bg-secondary', 'border-secondary', 'ring-secondary', 'hover:bg-secondary-dark', 'active:border-secondary', 'focus:border-secondary');
                });
                ele.classList.add('active', 'bg-secondary', 'border-secondary', 'ring-secondary', 'hover:bg-secondary-dark', 'active:border-secondary', 'focus:border-secondary');
            },
            add_field(event) {
                if(this.selected_field_type != '') {
                    create_field(event);
                }
            },
            copy_field(id) {

                let field = document.querySelector('[data-id="'+id+'"]');
                let new_field = field.outerHTML;
                let old_top = field.offsetTop;
                let new_id = Math.round(Date.now() * Math.random() * 100);
                let find_id = new RegExp(id, 'g');
                let field_type = field.getAttribute('data-type');

                new_field = new_field.replace(find_id, new_id);
                field.closest('.form-page-container').innerHTML += new_field;
                new_field = document.querySelector('[data-id="'+new_id+'"]');
                new_field.style.top = old_top + 30+'px';

                let active_field = document.querySelector('.page-container').__x.$data.active_field;
                setTimeout(function() {
                    console.log(active_field);
                    active_field = new_id;
                    console.log(active_field);
                    coordinates(null, new_field, field_type, 'field_copied');
                    drag_resize();

                    setTimeout(function() {
                        set_options_side(new_field);
                    }, 100);
                }, 200);

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

            }

        }
    }

    window.create_field = function (event) {

        let container = event.target.parentNode;
        let field_type = document.querySelector('.page-container').__x.$data.selected_field_type;
        let coords = coordinates(event, null, field_type, 'create_field');
        let active_page = document.querySelector('.page-container').__x.$data.active_page;

        let field_html = document.querySelector('#field_template').innerHTML;
        let id = Math.round(Date.now() * Math.random() * 100);

        field_html = field_html.replace(/%%id%%/g, id);
        field_html = field_html.replace(/%%type%%/g, field_type);
        field_html = field_html.replace(/%%x_perc%%/g, coords.x_perc);
        field_html = field_html.replace(/%%y_perc%%/g, coords.y_perc);
        field_html = field_html.replace(/%%h_perc%%/g, coords.h_perc);
        field_html = field_html.replace(/%%w_perc%%/g, coords.w_perc);
        container.innerHTML += field_html;

        let new_field = document.querySelector('[data-id="' + id + '"]');
        new_field.classList.add('drag-resize');


        if(field_type == 'radio') {
            new_field.querySelector('.field-name').classList.add('rounded-full');
            new_field.querySelector('.resizers').classList.add('rounded-full');
        }

        coordinates(null, new_field, field_type, 'field_created');
        drag_resize();

        setTimeout(function() {
            set_options_side(new_field);
        }, 1);

    }


    function drag_resize() {

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
            let field_type = element.getAttribute('data-type');

            draggable(element, field_type);

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
                    keep_aspect = field_type == 'radio' || field_type == 'checkbox' ? true : false;
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
                            console.log(original_y, e.pageY, original_mouse_y);
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

                    set_options_side(element);

                }

                function stopResize() {
                    window.removeEventListener('mousemove', resize);
                    coordinates(null, element, field_type, 'stopResize');
                    draggable(element, field_type);
                }
            }

        });

    }

    function set_options_side(element) {
        let width = element.parentNode.offsetWidth;
        let left = element.offsetLeft;
        if(left > (width / 2)) {
            element.__x.$data.options_side = 'right';
        } else {
            element.__x.$data.options_side = 'left';
        }
    }

    function draggable(element, field_type) {
        if(element.parentNode) {
            let draggable = new PlainDraggable(element, {
                handle: element.querySelector('.draggable-handle'),
                //autoScroll: true,
                //containment: document.querySelector('.form-page-container'),
                leftTop: true,
                onDrag: function(newPosition) {
                    set_options_side(element);
                },
                onDragEnd: function(newPosition) {
                    coordinates(null, element, field_type, 'onDragEnd');
                }
            });
        }
    }

    function coordinates(event, ele = null, field_type, function_name) {

        //console.log(function_name, event, ele, field_type);
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

        x = x - 1;
        y = y - 2;
        // convert to percent
        if(!container) {
            //console.log('missing', ele);
            return false;
        }
        let x_perc = pix_2_perc('x', x, container);
        let y_perc = pix_2_perc('y', y, container);

        //set heights
        let ele_h_perc = 1.3;
        if (field_type == 'radio' || field_type == 'checkbox') {
            ele_h_perc = 1.1;
        }
        if (event) {
            // remove element height from top position
            y_perc = y_perc - ele_h_perc;
        }

        // set w and h for new field
        let h_perc, w_perc;
        if (field_type == 'radio' || field_type == 'checkbox') {
            h_perc = 1.1;
            w_perc = 1.45;
        } else {
            h_perc = 1.3;
            if (ele) {
                w_perc = (ele.offsetWidth / container.offsetWidth) * 100;
            } else {
                w_perc = 15;
            }

        }
        h_perc = parseFloat(h_perc);
        w_perc = parseFloat(w_perc);

        if (ele) {

            // field data percents
            ele.setAttribute('data-h-perc', h_perc);
            ele.setAttribute('data-w-perc', w_perc);
            ele.setAttribute('data-x-perc', x_perc);
            ele.setAttribute('data-y-perc', y_perc);
            ele.setAttribute('data-y-perc', y_perc);
            ele.setAttribute('data-x', x);
            ele.setAttribute('data-y', y);

        }

        return {
            h_perc: h_perc,
            w_perc: w_perc,
            x_perc: x_perc,
            y_perc: y_perc
        }

    }

    function pix_2_perc(type, px, container) {
        if (type == 'x') {
            return (100 * parseFloat(px / parseFloat(container.offsetWidth)));
        } else {
            return (100 * parseFloat(px / parseFloat(container.offsetHeight)));
        }
    }

    window.select_common_name = function(event) {

        let ele = event.currentTarget;
        let id = ele.getAttribute('data-id');
        let name = ele.getAttribute('data-name');
        let field_div = ele.closest('.field-div');
        field_div.querySelector('.common-name-input').value = name;
        field_div.setAttribute('data-common-name-id').value = id;
        field_div.querySelector('.field-name').innerText = name;
        document.querySelector('.page-container').__x.$data.active_field = '';

    }

}

