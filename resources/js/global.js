import Toastr from 'toastr2';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import Sortable from 'sortablejs/modular/sortable.complete.esm.js';

window.Sortable = Sortable;
window.toastr = new Toastr();
toastr.options.preventDuplicates = true;
toastr.options.closeButton = true;
window.tippy = tippy;

tippy('[data-tippy-content]', {
    allowHTML: true,
});


window.addEventListener('load', (event) => {

    global_format_money();

    setInterval(global_format_phones, 1000);

    form_elements();

    setInterval(form_elements, 1000);

    numbers_only();

    document.querySelectorAll('.filepond--credits').forEach(function (div) {
        div.style.display = 'none';
    });



});


window._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// axios headersObj
window.axios_options = {
    headers: {
        'X-CSRF-TOKEN': _token
    }
};

if (document.URL.match(/tpuserportal.com\/[a-z]+/)) {

    if (!document.URL.match(/(login|forgot-password|register)/)) {
        setInterval(function checkSession() {
            axios.get('/is-logged-in')
                .then(function (response) {
                })
                .catch(function (error) {
                    if (error) {
                        location.reload();
                    }
                });
        }, 10000);
    }
}

window.form_elements = function () {

    document.querySelectorAll('.form-element').forEach(function (element) {

        let classes = element.classList;

        let size = '';
        if (classes.contains('sm')) {
            size = 'sm';
        } else if (classes.contains('md')) {
            size = 'md';
        } else if (classes.contains('lg')) {
            size = 'lg';
        } else if (classes.contains('xl')) {
            size = 'xl';
        }

        if (!classes.contains('label-added') && !classes.contains('range')) {

            let type = classes.contains('checkbox') || classes.contains('radio') ? 'checkbox' : 'text';
            if (element.getAttribute('type') == 'file') {
                type = 'file';
            }

            let label = '';
            let label_text = element.getAttribute('data-label');
            let parent = element.parentNode;

            if (element.parentNode.tagName !== 'LABEL' && element.getAttribute('type') != 'file') {
                label = document.createElement('LABEL');
                //parent.replaceChild(label, element);
                if (type == 'checkbox') {
                    parent.append(label);
                } else {
                    parent.prepend(label);
                }
            } else {
                label = element.parentNode;
            }
            if (!size) {
                size = 'md';
            }
            label.classList.add(type, size);

            let element_id = element.id ? element.id : 'input_' + (Date.now() * Math.random() * 1000).toFixed(0);
            element.id = element_id;

            if (label_text) {
                label.classList.add('form-element-label');
                label.setAttribute('for', element_id);
                if (type == 'checkbox') {
                    label.classList.add('mt-2');
                    label.insertAdjacentHTML('beforeend', '<div class="label-text inline-block">' + label_text + '</div>');
                    if (size == 'xl') {
                        element.classList.add('align-text-bottom');
                    }
                } else {
                    label.innerHTML = label_text;
                }
            }

            if (element.getAttribute('type') == 'file') {
                label = document.createElement('LABEL');
                let html = ' \
                <div class="flex justify-start w-full"> \
                    <div class="flex items-center bg-primary text-white text-sm p-2 whitespace-nowrap rounded-l"> \
                        <i class="fad fa-upload mr-2"></i> Select Files \
                    </div> \
                    <div class="flex-1"> \
                        <div class="file-names ' + element_id + ' text-xs max-h-24 overflow-y-auto p-2 w-full"></div> \
                    </div> \
                </div>';
                label.innerHTML = html;
                label.classList.add('form-element-label', 'file');
                label.setAttribute('for', element_id);
                parent.append(label);
            }

            classes.add('label-added');

            if (element.hasAttribute('required') || element.classList.contains('required')) {
                element.parentNode.insertAdjacentHTML('beforeend', '<div class="relative"> <span class="text-red-500 text-xxs error-message h-4 inline-block absolute top-0"></span> </div>');
            }

            // if(classes.contains('select')) {
            //     let cancel_div = document.createElement('div');
            //     cancel_div.classList.add('absolute', 'right-8', 'top-8', 'cancel-div', 'hidden');
            //     let html = ' \
            //     <a href="javascript:void(0)" @click="clear_select($el)"><i class="fal fa-times text-gray-400"></i></a>';
            //     cancel_div.innerHTML = html;
            //     element.parentNode.classList.add('relative');
            //     element.parentNode.append(cancel_div);

            //     element.addEventListener('change', function() {
            //         cancel_div.classList.add('hidden');
            //         if(element.value != '') {
            //             cancel_div.classList.remove('hidden');
            //         }
            //     });
            // }

        }

    });

}

window.clear_select = function (ele) {
    ele.closest('.relative').querySelector('select').value = '';
    ele.closest('.cancel-div').classList.add('hidden');
}

window.show_loading = function () {
    document.querySelector('.page-loading').classList.remove('hidden');
    document.querySelector('.page-loading').classList.add('block');
}
window.hide_loading = function () {
    document.querySelector('.page-loading').classList.add('hidden');
    document.querySelector('.page-loading').classList.remove('block');
}

window.ele_loading = function (ele) {
    ele.innerHTML = ' \
    <div class="w-full h-full absolute top-0 left-0 flex justify-around items-center bg-white opacity-75 z-50"> \
        <span class="text-gray-700 opacity-75"> \
            <i class="fas fa-circle-notch fa-spin fa-4x"></i> \
        </span> \
    </div>';
}

window.main_search = function () {

    return {

        show_search_results_div: false,

        search(search_input) {

            let scope = this;
            let value = search_input.value;
            let search_results_div = scope.$refs.search_results_div;

            if (value.length > 0) {

                axios.get('/search', {
                    params: {
                        value: value
                    },
                })
                    .then(function (response) {
                        if (response) {
                            search_results_div.innerHTML = response.data;
                            scope.show_search_results_div = true;

                        } else {
                            scope.show_search_results_div = false;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            } else {
                scope.show_search_results_div = false;
            }

        }

    }

}

window.display_errors = function (error, ele, button_html) {
    if (error) {
        if (error.response) {
            if (error.response.status == 422) {
                let errors = error.response.data.errors;
                show_form_errors(errors);
                ele.innerHTML = button_html;
            }
        }
    }
}

window.show_form_errors = function (errors) {

    remove_form_errors();

    Object.entries(errors).forEach(([key, value]) => {

        let field = `${key}`;
        value = `${value}`;
        let message = value;
        if (Array.isArray(value)) {
            message = value[0];
        }

        let element = '';
        if (field.match(/\.[0-9]+$/)) {
            let fields = field.split('.');
            let elements = document.querySelectorAll('[name="' + fields[0] + '[]"]');
            element = elements[fields[1]];
        } else {
            element = document.querySelector('[name="' + field + '"]');
        }
        let label = '';
        let error_message = '<div class="error-message text-red-500 text-xxs">' + message + '</div>';

        if (element) {
            if (element.parentNode.tagName == 'LABEL') {
                label = element.parentNode;
                label.insertAdjacentHTML('beforeend', error_message);
            } else {
                label = element.parentNode.querySelector('label');
                element.parentNode.insertAdjacentHTML('beforeend', error_message);
            }



            //let error_message = element.closest('label').querySelector('.error-message');
            //error_message.innerHTML = message;
            scroll_above(element);
            toastr.error('Field not completed');
        }

    });

}

window.remove_form_errors = function (event = null) {

    if (event) {
        let label = event.target.closest('label');
        label.querySelector('.error-message').innerHTML = '';
        label.querySelector('.error-message').classList.toggle('hidden');
    } else {
        document.querySelectorAll('.error-message').forEach(function (error_div) {
            error_div.innerHTML = '';
        });
    }
}


window.text_editor = function (options) {

    const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

    if (options.selector == '') {
        options.selector = '.text-editor';
    }
    options.lists_indent_on_tab = true;
    // options.content_css = '/css/tinymce.css';
    options.content_style = "body { font-family: Arial; }";
    options.branding = false;
    // options.images_upload_handler = image_upload_handler;
    // options.newline_behavior = 'invert';
    // options.fix_list_elements = true;
    // options.forced_root_block = '<br>';
    options.toolbar_sticky = true;
    // options.toolbar_sticky_offset = isSmallScreen ? 102 : 108;

    /* enable title field in the Image dialog*/
    options.image_title = true;
    /* enable automatic uploads of images represented by blob or data URIs*/
    options.automatic_uploads = true;
    /*
        URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
        images_upload_url: 'postAcceptor.php',
        here we add custom filepicker only to Image dialog
    */
    options.file_picker_types = 'image';
    /* and here's our custom image picker*/
    options.file_picker_callback = (cb, value, meta) => {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                /*
                    Note: Now we need to register the blob in TinyMCEs image blob
                    registry. In the next release this part hopefully won't be
                    necessary, as we are looking to handle it internally.
                */
                const id = 'blobid' + (new Date()).getTime();
                const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), { title: file.name });
            });
            reader.readAsDataURL(file);
        });

        input.click();
    }


    tinymce.remove(options.selector);
    tinymce.init(options);

    // select upload option on add image
    // setTimeout(function () {
    //     let insert_button = document.querySelector('[aria-label="Insert/edit image"]');
    //     if (insert_button) {
    //         document.querySelector('[aria-label="Insert/edit image"]').addEventListener('click', function () {
    //             setTimeout(function () {
    //                 document.querySelector('.tox-dialog__body-nav').lastChild.click();
    //             }, 500);
    //         });
    //     }
    // }, 1000);

}

window.image_upload_handler = function (blobInfo, success, failure, progress) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/text_editor/file_upload');

    xhr.upload.onprogress = function (e) {
        progress(e.loaded / e.total * 100);
    };

    xhr.onload = function () {
        var json;

        if (xhr.status === 403) {
            failure('HTTP Error: ' + xhr.status, {
                remove: true
            });
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300) {
            failure('HTTP Error: ' + xhr.status);
            return;
        }
        console.log(xhr.responseText);
        json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location != 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            return;
        }

        success(json.location);
    };

    xhr.onerror = function () {
        failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };

    formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    formData.append('_token', _token);

    xhr.send(formData);
};


window.scroll_above = function (element) {

    let yOffset = -150;
    let y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
    window.scrollTo({
        top: y,
        behavior: 'smooth'
    });
    element.focus({
        preventScroll: true
    });

}


window.show_loading_button = function (button, text) {
    button.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> ' + text;
}

window.decode_HTML = function (html) {
    let txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
};

window.randomHSL = function () {
    return `hsla(${~~(360 * Math.random())},70%,70%,0.8)`
}

window.truncate_string = function (str, num) {
    if (str.length > num) {
        return str.slice(0, num) + "...";
    } else {
        return str;
    }
}


window.get_location_details = function (container, member_id, zip, city, state, county = null) {


    if (member_id) {
        container = document.querySelector(container + '[data-id="' + member_id + '"]');
    } else {
        container = document.querySelector(container);
    }
    zip = container.querySelector(zip);
    city = container.querySelector(city);
    state = container.querySelector(state);
    county = container.querySelector(county) || null;

    let zip_code = zip.value;

    if (zip_code.length == 5) {
        axios.get('/transactions/get_location_details', {
            params: {
                zip: zip_code
            },
        })
            .then(function (response) {
                city.value = response.data.city;
                state.value = response.data.state;
                if (county) {
                    let event = new Event('change');
                    state.dispatchEvent(event);
                    setTimeout(function () {
                        county.value = response.data.county;
                    }, 200);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }
}

window.datatable_settings = {
    "autoWidth": false,
    "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
    ],
    "responsive": true,
    "destroy": true,
    "fixedHeader": true,
    "processing": true,
    "serverSide": true,
    "language": {
        search: '',
        searchPlaceholder: 'Search'
    },
    "language": {
        "info": "_START_ to _END_ of _TOTAL_",
        "lengthMenu": "Show _MENU_",
        "search": ""
    },

}

window.data_table = function (src, cols, page_length, table, sort_by, no_sort_cols, hidden_cols, show_buttons, show_search, show_info, show_paging, show_hide_cols = true, hide_header_and_footer = false) {

    /*
    src = 'url'
    table = $('#table_id')
    sort_by = [1, 'desc'] - col #, dir
    no_sort_cols = [0, 8] - array of cols
    hidden_cols = [0, 8] - array of cols
    show_buttons = true/false
    show_search = true/false
    show_info = true/false
    show_paging = true/false
    show_hide_cols = true/false
    hide_header_and_footer = true/false
    */

    datatable_settings.ajax = src;
    datatable_settings.columns = cols;
    datatable_settings.serverSide = true;
    datatable_settings.processing = true;


    datatable_settings.pageLength = parseInt(10);
    if (page_length != '') {
        datatable_settings.pageLength = parseInt(page_length);
    }

    if (sort_by.length > 0) {
        datatable_settings.order = [
            [sort_by[0], sort_by[1]]
        ];
    }

    if (no_sort_cols.length > 0) {
        datatable_settings.columnDefs = [{
            orderable: false,
            targets: no_sort_cols
        }];
    }

    if (hidden_cols.length > 0) {
        hidden_cols.forEach(function (col) {
            datatable_settings.columnDefs.push({
                targets: [col],
                visible: false
            });
        });
    }

    let buttons = '';

    if (show_buttons == true) {

        if (show_hide_cols == true) {
            datatable_settings.buttons = [{
                extend: 'colvis',
                text: 'Hide Columns'
            }];
        }

        datatable_settings.buttons.push({
            extend: 'excelHtml5',
            exportOptions: {
                columns: ':visible'
            }
        }, {
            extend: 'pdfHtml5',
            exportOptions: {
                columns: ':visible'
            }
        });
        buttons = '<B>';


    }



    let search = '';
    if (show_search == true) {
        search = '<f>';
    }

    let info = '';
    if (show_info == true) {
        info = '<i>';
    }

    let paging = '';
    let length = '';
    datatable_settings.paging = false;
    if (show_paging == true) {
        paging = '<p>';
        datatable_settings.paging = true;
        length = '<l>';
    }

    if (hide_header_and_footer == true) {
        datatable_settings.drawCallback = function () {
            $(this.api().table().header()).hide();
            $(this.api().table().footer()).hide();
        }
        info = '';
        paging = '';
        datatable_settings.paging = false;
        length = '';
        search = '';
        buttons = '';
        datatable_settings.buttons = [];
    }


    datatable_settings.dom = '<"flex justify-between flex-wrap items-center text-gray-600"' + search + info + length + buttons + '>rt<"flex justify-between items-center text-gray-600"' + info + paging + '>'

    let dt = table.DataTable(datatable_settings);


    style_dt_buttons();
    dt.on('draw', style_dt_buttons);


    return dt;

}

function style_dt_buttons() {
    $('.dataTables_filter [type="search"]').attr('placeholder', 'Search');
    $('.dt-button').attr('class', ' buttons-colvis px-2 py-1 bg-primary hover:bg-primary-dark active:bg-primary-dark focus:border-primary-dark ring-primary-dark inline-flex items-center border border-primary-dark rounded text-sm text-white tracking-tight focus:outline-none disabled:opacity-25 transition ease-in-out duration-150 shadow hover:shadow-md');


    $('.paginate_button').removeClass('paginate_button').addClass('paginate_button_custom');
}


// Format Money
window.global_format_number = function (num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 0
    });

    num = num.toString().replace(/[,\$]/g, '');
    return formatter.format(num);
}

window.global_format_number_with_decimals = function (num) {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    });

    num = num.replace(/[,\$]/g, '').toString();
    return formatter.format(num);
}

window.format_money = function (ele) {
    let val = ele.value.replace(/\$/, '');
    ele.value = '$' + global_format_number(val);
}

window.format_money_with_decimals = function (ele) {
    let val = ele.value.replace(/\$/, '');
    ele.value = global_format_number_with_decimals(val);
}

window.global_format_money = function () {
    // $('.money, .money-decimal').each(function() {
    //     let val = $(this).val();
    //     if(val.match(/[a-zA-Z]+/)) {
    //         $(this).val(val.replace(/[a-zA-Z]+/,''));
    //     }
    // });

    let money = document.querySelectorAll('.money');

    if (money.length > 0) {

        money.forEach(function (input) {

            if (input.value != '') {
                format_money(input);
            }
            input.onkeyup = function () {
                if (input.value != '') {
                    format_money(input);
                }
            }

        });

    }

    let money_decimal = document.querySelectorAll('.money-decimal');

    if (money_decimal.length > 0) {

        money_decimal.forEach(function (input) {

            if (input.value != '') {
                format_money_with_decimals(input);
            }
            input.onchange = function () {
                if (input.value != '') {
                    format_money_with_decimals(input);
                }
            }

        });

    }

}


document.querySelectorAll('.ssn').forEach(function (input) {

    input.addEventListener('keydown', (event) => {

        let re = /\D/g; // remove any characters that are not numbers
        let soc_sec = input.value.replace(re, "");

        let ssa = soc_sec.slice(0, 3);
        let ssb = soc_sec.slice(3, 5);
        let ssc = soc_sec.slice(5, 9);
        input.value = ssa + "-" + ssb + "-" + ssc;

    });

});


// Numbers Only
window.numbers_only = function () {

    document.querySelectorAll('.numbers-only').forEach(function (input) {

        input.addEventListener('keydown', (event) => {

            let allowed_keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ',', 'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Control', 'v', 'c'];

            if (!input.classList.contains('no-decimals')) {
                allowed_keys.push('.');
            }
            if (!allowed_keys.includes(event.key)) {
                event.preventDefault();
            } else {

                let max = input.getAttribute('max') || null;

                if (max) {
                    if (parseInt(input.value + event.key) > max) {
                        event.preventDefault();
                        input.value = event.key;
                    }
                }
            }

        });

    });

}

// Format Phone
window.global_format_phone = function (obj) {
    if (obj) {
        let numbers = obj.value.replace(/\D/g, ''),
            char = {
                0: '(',
                3: ') ',
                6: '-'
            };
        obj.value = '';
        for (let i = 0; i < numbers.length; i++) {
            if (i > 13) {
                return false;
            }
            obj.value += (char[i] || '') + numbers[i];
        }
    }
}
window.global_format_phones = function () {
    document.querySelectorAll('.phone').forEach(function (input) {
        input.classList.add('numbers-only');
        global_format_phone(input);
        input.setAttribute('maxlength', 14);
        input.addEventListener('keyup', (event) => {
            global_format_phone(input);
        })
    });
}

// unwrap element
window.unwrap = function (wrapper) {
    // place childNodes in document fragment
    var docFrag = document.createDocumentFragment();
    while (wrapper.firstChild) {
        var child = wrapper.removeChild(wrapper.firstChild);
        docFrag.appendChild(child);
    }

    // replace wrapper with document fragment
    wrapper.parentNode.replaceChild(docFrag, wrapper);
}

window.ucwords = function (str) {
    return (str + '')
        .replace(/^(.)|\s+(.)/g, function ($1) {
            return $1.toUpperCase()
        })
}

window.random_dark_color = function () {
    var lum = -0.25;
    var hex = String('#' + Math.random().toString(16).slice(2, 8).toUpperCase()).replace(/[^0-9a-f]/gi, '');
    if (hex.length < 6) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    var rgb = "#",
        c, i;
    for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i * 2, 2), 16);
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
        rgb += ("00" + c).substr(c.length);
    }
    return rgb;
}


window.global_get_url_parameters = function (key) {
    // usage
    // let tab = global_get_url_parameters('tab');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}

window.copy_to_clipboard = function (input) {
    let text = input.value;
    // navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard api method'
        return navigator.clipboard.writeText(text);
    } else {
        // text area method
        let textArea = document.createElement("textarea");
        textArea.value = text;
        // make the textarea out of viewport
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            // here the magic happens
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
}



class Badger {
    constructor(options) {
        Object.assign(
            this, {
            backgroundColor: "#f00",
            color: "#fff",
            size: 0.8, // 0..1 (Scale in respect to the favicon image size)
            position: "se", // Position inside favicon "n", "e", "s", "w", "ne", "nw", "se", "sw"
            radius: 10, // Border radius
            src: "", // Favicon source (dafaults to the <link> icon href)
            onChange() { },
        },
            options
        );
        this.canvas = document.createElement("canvas");
        this.src = this.src || this.faviconEL.getAttribute("href");
        this.ctx = this.canvas.getContext("2d");
    }

    faviconEL = document.querySelector("link[rel$=icon]");

    _drawIcon() {
        this.ctx.clearRect(0, 0, this.faviconSize, this.faviconSize);
        this.ctx.drawImage(this.img, 0, 0, this.faviconSize, this.faviconSize);
    }

    _drawShape() {
        const r = this.radius;
        const xa = this.offset.x;
        const ya = this.offset.y;
        const xb = this.offset.x + this.badgeSize;
        const yb = this.offset.y + this.badgeSize;
        this.ctx.beginPath();
        this.ctx.moveTo(xb - r, ya);
        this.ctx.quadraticCurveTo(xb, ya, xb, ya + r);
        this.ctx.lineTo(xb, yb - r);
        this.ctx.quadraticCurveTo(xb, yb, xb - r, yb);
        this.ctx.lineTo(xa + r, yb);
        this.ctx.quadraticCurveTo(xa, yb, xa, yb - r);
        this.ctx.lineTo(xa, ya + r);
        this.ctx.quadraticCurveTo(xa, ya, xa + r, ya);
        this.ctx.fillStyle = this.backgroundColor;
        this.ctx.fill();
        this.ctx.closePath();
    }

    _drawVal() {
        const margin = (this.badgeSize * 0.18) / 2;
        this.ctx.beginPath();
        this.ctx.textBaseline = "middle";
        this.ctx.textAlign = "center";
        this.ctx.font = `bold ${this.badgeSize * 0.82}px Arial`;
        this.ctx.fillStyle = this.color;
        this.ctx.fillText(this.value, this.badgeSize / 2 + this.offset.x, this.badgeSize / 2 + this.offset.y + margin);
        this.ctx.closePath();
    }

    _drawFavicon() {
        this.faviconEL.setAttribute("href", this.dataURL);
    }

    _draw() {
        this._drawIcon();
        if (this.value) this._drawShape();
        if (this.value) this._drawVal();
        this._drawFavicon();
    }

    _setup() {
        this.faviconSize = this.img.naturalWidth;
        this.badgeSize = this.faviconSize * this.size;
        this.canvas.width = this.faviconSize;
        this.canvas.height = this.faviconSize;
        const sd = this.faviconSize - this.badgeSize;
        const sd2 = sd / 2;
        this.offset = {
            n: {
                x: sd2,
                y: 0
            },
            e: {
                x: sd,
                y: sd2
            },
            s: {
                x: sd2,
                y: sd
            },
            w: {
                x: 0,
                y: sd2
            },
            nw: {
                x: 0,
                y: 0
            },
            ne: {
                x: sd,
                y: 0
            },
            sw: {
                x: 0,
                y: sd
            },
            se: {
                x: 8,
                y: 8
            },
        }[this.position];
    }

    // Public functions / methods:

    update() {
        this._value = Math.min(99, parseInt(this._value, 10));
        if (this.img) {
            this._draw();
            if (this.onChange) this.onChange.call(this);
        } else {
            this.img = new Image();
            this.img.addEventListener("load", () => {
                this._setup();
                this._draw();
                if (this.onChange) this.onChange.call(this);
            });
            this.img.src = this.src;
        }
    }

    get dataURL() {
        return this.canvas.toDataURL();
    }

    get value() {
        return this._value;
    }

    set value(val) {
        this._value = val;
        this.update();
    }
}


const myBadgerOptions = {}; // See: constructor for customization options
const myBadger = new Badger(myBadgerOptions);
setInterval(() => {
    axios.get('/marketing/get_notification_count')
        .then(function (response) {
            let count = response.data.count;
            myBadger.value = count;
        })
        .catch(function (error) {
            console.log(error);
        });
}, 10000);




