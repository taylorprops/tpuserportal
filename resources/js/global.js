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

window.ele_loading = function(ele) {
    ele.html(' \
    <div class="page-loading w-full h-full fixed block top-0 left-0 bg-white opacity-75 z-50"> \
        <span class="text-gray-700 opacity-75 top-1/3 my-0 mx-auto block relative w-0 h-0"> \
            <i class="fas fa-circle-notch fa-spin fa-4x"></i> \
        </span> \
    </div>');
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

window.randomHSL = function(){
    return `hsla(${~~(360 * Math.random())},70%,70%,0.8)`
}

window.datatable_settings = {
    "autoWidth": false,
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "responsive": true,
    "destroy": true,
    fixedHeader: true,
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
    $('.dt-button').attr('class', ' buttons-colvis px-2 py-1 bg-primary hover:bg-primary-dark active:bg-primary-dark focus:border-primary-dark ring-primary-dark inline-flex items-center border border-primary-dark rounded text-sm text-white tracking-tight focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150 shadow hover:shadow-md');


    $('.paginate_button').removeClass('paginate_button').addClass('paginate_button_custom');
}
