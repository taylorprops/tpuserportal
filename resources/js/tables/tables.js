
window.table = function(options) {

    let table = options.table;
    let data_url = options.data_url;
    let active = options.active == true ? 'yes' : null;
    let sort_by = options.sort_by || null;
    let length = options.length || null;

    return {

        search_val: '',
        active_url: data_url,
        page_url: data_url,
        active: active,
        sort_by: sort_by,
        table: table,
        length: length,

        init() {
            show_loading();
            table_show_active(this, 'yes');
        },
        init_table_change_length(val) {
            table_change_length(this, val);
        },
        init_table_show_active(val) {
            table_show_active(this, val);
        },
        init_table_search(val) {
            table_search(this, val);
        },

    }

}

window.table_init = function(scope, url) {

    if(!url) {
        url = scope.page_url;
    }
    scope.active_url = url;
    axios.get(url)
    .then(function (response) {
        document.querySelector(scope.table).innerHTML = response.data;
        //table_links(scope);
        hide_loading();
    })
    .catch(function (error) {
        console.log(error);
    });

}

window.table_links = function(scope) {

    let table = scope.table;
    let container = document.querySelector(table);

    let container_table = container.querySelector('table');
    container_table.querySelectorAll('th').forEach(function(th) {
        if(th.querySelector('a')) {
            th.querySelector('a').classList.add('sort-by');
        }
    });

    document.querySelector(table).querySelectorAll('a').forEach(function(link) {
        if(!link.classList.contains('view-link')) {
            link.addEventListener('click', function(e) {

                show_loading();
                e.preventDefault();

                let url = link.href+'&length='+scope.length;
                let href = new URL(url);
                let params = new URLSearchParams(href.search);

                if(scope.search_val != '') {
                    if(url.match(/\?/)) {
                        url += '&';
                    } else {
                        url += '?';
                    }

                    params.delete('search');
                    url += 'search=' + scope.search_val;
                }

                if(scope.active != '') {
                    if(url.match(/\?/)) {
                        url += '&';
                    } else {
                        url += '?';
                    }

                    params.delete('active');
                    url += 'active=' + scope.active;
                }

                if(link.classList.contains('sort-by')) {
                    scope.sort_by = params.get('sort');
                }

                url += '&sort=' + scope.sort_by;
                scope.active_url = url;
                table_init(scope, url);
            });
        }
    });
}

window.table_search = function(scope, val) {
    scope.search_val = val;
    scope.active = '';
    if(document.querySelector('#table_show_active')) {
        document.querySelector('#table_show_active').value = '';
    }
    scope.active_url = scope.page_url+'?search=' + val.trim();
    table_init(scope, scope.active_url);
}

window.table_show_active = function(scope, active) {

    scope.active = active;
    scope.active_url = scope.active_url.replace(/[&]*active=[a-z]*/, '');
    if(scope.active_url.match(/\?/)) {
        scope.active_url += '&';
    } else {
        scope.active_url += '?';
    }
    scope.active_url += 'active=' + active;
    table_init(scope, scope.active_url);
}

window.table_change_length = function(scope, val) {
    console.log(val);
    length = parseInt(val);
    scope.length = length;
    scope.active_url = scope.active_url.replace(/[&]*length=[a-z]*/, '');
    if(scope.active_url.match(/\?/)) {
        scope.active_url += '&';
    } else {
        scope.active_url += '?';
    }
    scope.active_url += 'length=' + length;
    table_init(scope, scope.active_url);
}
