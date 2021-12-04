
window.table = function(options) {

    let container = options.container;
    let data_url = options.data_url;
    let active = options.active == true ? 'yes' : null;
    let sort = options.sort_by || null;
    let length = options.length || null;
    let button = options.button || null;

    return {

        container: container,
        search_val: '',
        active_url: data_url,
        page_url: data_url,
        button: button,
        active: active,
        sort: sort,
        direction: 'asc',
        length: length,
        page: '1',

        init() {
            show_loading();
            this.init_table();
            this.load_table();
            this.show_options();
        },

        init_table() {
            this.container.innerHTML = ' \
            <div class="options-container"></div> \
            <div class="table-container"></div> \
            ';
        },

        load_table(url = null) {

            let scope = this;

            if(!url) {
                url = scope.page_url;
            }
            scope.active_url = url;

            axios.get(url)
            .then(function (response) {
                scope.container.querySelector('.table-container').innerHTML = response.data;
                hide_loading();
                scope.table_links();
            })
            .catch(function (error) {
                console.log(error);
            });




        },

        show_options() {

            let scope = this;

            options_div = document.createElement('div');

            let options_html = '<div class="flex justify-between options-div"> \
            <div class="flex">';

            options_html += ' \
            <div class="p-2 ml-6 w-36"> \
                <input \
                type="text" \
                class="form-element input md" \
                    data-label="Search" \
                    x-on:keyup="option_search($el.value)"> \
            </div> \
            ';

            if(scope.active) {

                options_html += ' \
                <div class="p-2 ml-6 w-36"> \
                    <select \
                    class="form-element select md" \
                    data-label="Active" \
                    x-ref="active" \
                    x-on:change="option_show_active($el.value)"> \
                        <option value="all">All</option> \
                        <option value="yes" selected>Active</option> \
                        <option value="no">Not Active</option> \
                    </select> \
                </div> \
                ';

            }

            options_html += '</div>';

            if(scope.button) {
                console.log(scope.button.url);

                options_html += ' \
                <div> \
                    <a href="'+scope.button.url+'" target="_blank" class="button primary lg">'+scope.button.html+'</a> \
                </div> \
                ';
            }

            options_html += '</div>';

            options_div.innerHTML = options_html;

            scope.container.querySelector('.options-container').append(options_div);

        },

        table_links() {

            let scope = this;
            let table = scope.container.querySelector('table');

            table.querySelectorAll('th').forEach(function(th) {
                if(th.querySelector('a')) {
                    th.querySelector('a').classList.add('sort-by');
                }
            });


            scope.container.querySelectorAll('.pagination-link, .sort-by').forEach(function(link) {

                link.addEventListener('click', function(e) {

                    //show_loading();
                    e.preventDefault();

                    let url = link.href;
                    let href = new URL(url);
                    let params = new URLSearchParams(href.search);

                    /*
                        URL params
                        sort, direction, page, length, search, active
                    */

                    if(link.classList.contains('sort-by')) {

                        scope.sort = params.get('sort');
                        scope.direction = params.get('direction');
                        url += '&search='+scope.search_val+'&active='+scope.active+'&length='+scope.length+'&page='+scope.page;

                    } else {

                        scope.page = params.get('page');
                        url += '&search='+scope.search_val+'&active='+scope.active+'&length='+scope.length+'&sort='+scope.sort+'&direction='+scope.direction;

                    }

                    scope.active_url = url;
                    scope.load_table(url);

                });

            });

        },

        option_search(val) {

            let scope = this;

            scope.search_val = val;
            scope.active = '';

            if(scope.$refs.active) {
                scope.$refs.active.value = '';
            }

            let url = scope.add_url_param('search', val.trim());

            scope.active_url = url;
            scope.load_table(url);

        },

        option_show_active(active) {

            let scope = this;

            scope.active = active;

            let url = scope.add_url_param('active', active);

            scope.active_url = url;
            scope.load_table(url);

        },

        table_change_length(val) {

            let scope = this;

            length = parseInt(val);
            scope.length = length;

            let url = scope.add_url_param('length', length);

            scope.active_url = url;
            scope.load_table(url);

        },

        add_url_param(key, val) {

            let scope = this;

            let url = scope.page_url+'?search='+scope.search_val+'&active='+scope.active+'&length='+scope.length+'&sort='+scope.sort+'&direction='+scope.direction;
            let param = new RegExp('[&]*'+key+'=[a-zA-Z0-9_]*');
            url = url.replace(param, '');
            url += '&'+key+'='+val;
            return url;

        }

    }

}

