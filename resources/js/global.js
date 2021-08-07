import Toastr from 'toastr2';
window.toastr = new Toastr();
toastr.options.preventDuplicates = true;



window.addEventListener('load', (event) => {

    global_format_money();

    setInterval(global_format_phones, 1000);

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



window.show_loading = function() {
    document.querySelector('.page-loading').classList.remove('hidden');
    document.querySelector('.page-loading').classList.add('block');
}
window.hide_loading = function() {
    document.querySelector('.page-loading').classList.add('hidden');
    document.querySelector('.page-loading').classList.remove('block');
}

window.ele_loading = function(ele) {
    ele.html(' \
    <div class="page-loading w-full h-full fixed block top-0 left-0 bg-white opacity-75 z-50"> \
        <span class="text-gray-700 opacity-75 top-1/3 my-0 mx-auto block relative w-0 h-0"> \
            <i class="fas fa-circle-notch fa-spin fa-4x"></i> \
        </span> \
    </div>');
}

window.show_form_errors = function(errors) {
    remove_form_errors();
    Object.entries(errors).forEach(([key, value]) => {
        let field = `${key}`;
        let message = `${value}`;
        let element = document.querySelector('#'+field);
        if(element) {
            let error_message = element.closest('label').querySelector('.error-message');
            error_message.innerHTML = message;
            element.scrollIntoView();
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

window.randomHSL = function(){
    return `hsla(${~~(360 * Math.random())},70%,70%,0.8)`
}

window.datatable_settings = {
    "autoWidth": false,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "responsive": true,
    "destroy": true,
    "fixedHeader": true,
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

window.data_table = function(src, cols, page_length, table, sort_by, no_sort_cols, hidden_cols, show_buttons, show_search, show_info, show_paging, show_hide_cols = true, hide_header_and_footer = false) {

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
    if(page_length != '') {
        datatable_settings.pageLength = parseInt(page_length);
    }

    if(sort_by.length > 0) {
        datatable_settings.order = [[sort_by[0], sort_by[1]]];
    }

    if(no_sort_cols.length > 0) {
        datatable_settings.columnDefs = [{
            orderable: false,
            targets: no_sort_cols
        }];
    }

    if(hidden_cols.length > 0) {
        hidden_cols.forEach(function(col) {
            datatable_settings.columnDefs.push({
                targets: [col],
                visible: false
            });
        });
    }

    let buttons = '';

    if(show_buttons == true) {

        if(show_hide_cols == true) {
            datatable_settings.buttons = [
                {
                    extend: 'colvis',
                    text: 'Hide Columns'
                }
            ];
        }

        datatable_settings.buttons.push(
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            }
        );
        buttons = '<B>';


    }



    let search = '';
    if(show_search == true) {
        search = '<f>';
    }

    let info = '';
    if(show_info == true) {
        info = '<i>';
    }

    let paging = '';
    let length = '';
    datatable_settings.paging = false;
    if(show_paging == true) {
        paging = '<p>';
        datatable_settings.paging = true;
        length = '<l>';
    }

    if(hide_header_and_footer == true) {
        datatable_settings.drawCallback = function() {
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


    datatable_settings.dom = '<"flex justify-between flex-wrap items-center text-gray-600"'+search+info+length+buttons+'>rt<"flex justify-between items-center text-gray-600"'+info + paging+'>'

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
        style: 'currency', currency: 'USD'
    });

    num = num.replace(/[,\$]/g, '').toString();
    return formatter.format(num);
}

window.format_money = function(ele) {
    let val = ele.value.replace(/\$/,'');
    ele.value = '$'+global_format_number(val);
}

window.format_money_with_decimals = function(ele) {
    let val = ele.value.replace(/\$/,'');
    ele.value = global_format_number_with_decimals(val);
}

window.global_format_money = function() {
    // $('.money, .money-decimal').each(function() {
    //     let val = $(this).val();
    //     if(val.match(/[a-zA-Z]+/)) {
    //         $(this).val(val.replace(/[a-zA-Z]+/,''));
    //     }
    // });

    let money = document.querySelectorAll('.money');

    if(money.length > 0) {

        money.forEach(function(input) {

            if(input.value != '') {
                format_money(input);
            }
            input.onkeyup = function () {
                if(input.value != '') {
                    format_money(input);
                }
            }

        });

    }

    let money_decimal = document.querySelectorAll('.money-decimal');

    if(money_decimal.length > 0) {

        money_decimal.forEach(function(input) {

            if(input.value != '') {
                format_money_with_decimals(input);
            }
            input.onchange = function() {
                if(input.value != '') {
                    format_money_with_decimals(input);
                }
            }

        });

    }

}


// Numbers Only
document.querySelectorAll('.numbers-only').forEach(function(input) {

    input.addEventListener('keydown', (event) => {

        // set attr  max with input type = text
        let allowed_keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ',', 'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Control', 'v'];

        if(!input.classList.contains('no-decimals')) {
            allowed_keys.push('.');
        }
        if(!allowed_keys.includes(event.key)) {
            event.preventDefault();
        } else {

            let max = input.getAttribute('max') ?? null;

            if(max) {
                if(parseInt(input.value + event.key) > max) {
                    event.preventDefault();
                    input.value = event.key;
                }
            }
        }

    });

});

// Format Phone
window.global_format_phone = function (obj) {
    if(obj) {
        let numbers = obj.value.replace(/\D/g, ''),
            char = { 0: '(', 3: ') ', 6: '-' };
        obj.value = '';
        for (let i = 0; i < numbers.length; i++) {
            if (i > 13) {
                return false;
            }
            obj.value += (char[i] || '') + numbers[i];
        }
    }
}
window.global_format_phones = function() {
    document.querySelectorAll('.phone').forEach(function(input) {
        input.classList.add('numbers-only');
        global_format_phone(input);
        input.setAttribute('maxlength', 14);
        input.addEventListener('keyup', (event) => {
            global_format_phone(input);
        })
    });
}

// unwrap element
window.unwrap = function(wrapper) {
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

window.random_dark_color = function() {
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


window.global_get_url_parameters = function(key) {
    // usage
    // let tab = global_get_url_parameters('tab');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}
