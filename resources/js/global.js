import Toastr from 'toastr2';
window.toastr = new Toastr();
toastr.options.preventDuplicates = true;


window.addEventListener('load', (event) => {
});

window._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// axios headersObj
window.axios_options = {
    headers: { 'X-CSRF-TOKEN': _token }
};


// Add a response interceptor

/* axios.interceptors.response.use(function (response) {

    if(response.data.message) {
        if(response.data.message.match(/Unauthenticated/)) {
            window.location.href = '/login';
        }
    }
    return response;

}, function (error) {
    console.log(error);
    if(error.data.message) {
        if(error.data.message.match(/Unauthenticated/)) {
            window.location.href = '/login';
        }
    }

}); */


window.show_loader = function() {
    document.querySelector('body').__x.$data.show_loading = true;
}
window.hide_loader = function() {
    document.querySelector('body').__x.$data.show_loading = false;
}



window.show_form_errors = function(errors) {
    Object.entries(errors).forEach(([key, value]) => {
        let field = `${key}`;
        let message = `${value}`;
        let element = document.querySelector('#'+field);
        if(element) {
            let error_message = element.closest('label').querySelector('.error-message');
            error_message.innerHTML = message;
        }

    });
}

window.remove_form_errors = function(event = null) {

    if(event) {
        let label = event.target.closest('label');
        label.querySelector('.error-message').innerHTML = '';
        label.querySelector('.error-message').classList.toggle('hidden');
    } else {
        document.querySelectorAll('.error-message').forEach(function(error_div) {
            error_div.innerHTML = '';
        });
    }
}


window.show_loading_button = function(button, text) {
    button.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> '+text;
}

window.decode_HTML = function (html) {
	var txt = document.createElement('textarea');
	txt.innerHTML = html;
	return txt.value;
};


/* window.drag_resize = function() {

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
            }

            function stopResize() {

                window.removeEventListener('mousemove', resize);
                coordinates(null, element, field_type, 'stopResize');
                draggable(element, field_type);
            }
        }

    });

}

window.draggable = function(element, field_type) {
    element.classList.remove('plain-draggable');
    let draggable = new PlainDraggable(element, {
        handle: element.querySelector('.draggable-handle'),
        //autoScroll: true,
        leftTop: true,
        onDragEnd: function(newPosition) {
            coordinates(null, element, field_type, 'onDragEnd');
        }
    });
}

window.coordinates = function(event, ele = null, field_type, function_name) {

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

window.pix_2_perc = function(type, px, container) {
    if (type == 'x') {
        return (100 * parseFloat(px / parseFloat(container.offsetWidth)));
    } else {
        return (100 * parseFloat(px / parseFloat(container.offsetHeight)));
    }
} */
