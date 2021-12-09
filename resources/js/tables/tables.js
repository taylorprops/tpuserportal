
window.table = function(options) {

    let container = options.container;
    let data_url = options.data_url;
    let active = options.active ? options.active : null;
    let sort = options.sort_by || null;
    let length = options.length || null;
    let button_export = options.button_export || false;
    let export_url = options.export_url || null;
    let buttons = options.buttons || null;

    return {

        container: container,
        search_val: '',
        active_url: data_url,
        page_url: data_url,
        button_export: button_export,
        export_url: export_url,
        buttons: buttons,
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
            this.active_url = this.add_url_param('active', this.active);
        },

        load_table(url = null, to_excel = false) {

            let scope = this;

            if(!url) {
                url = scope.page_url+'?active='+scope.active;
            }
            scope.active_url = url;

            axios.get(url, {
                params: {
                    to_excel: to_excel
                },
            })
            .then(function (response) {
                if(to_excel == false) {
                    scope.container.querySelector('.table-container').innerHTML = response.data;
                    scope.table_links();
                } else {
                    window.location = response.data.file;
                }
                hide_loading();

                scope.container.querySelectorAll('tr:not(first-child)').forEach(function(row) {
                    row.classList.add('hover:bg-gray-50');
                });

            })
            .catch(function (error) {
                console.log(error);
            });

        },

        to_excel() {
            show_loading();
            this.load_table(this.active_url, true);
        },

        show_options() {

            let scope = this;

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
                    x-on:change="option_show_active($el.value);"> \
                        <option value="all" \
                        ';
                        if(scope.active == 'all') {
                            options_html += ' selected';
                        }
                        options_html += '>All</option> \
                        <option value="yes" \
                        ';
                        if(scope.active == 'yes') {
                            options_html += ' selected';
                        }
                        options_html += '>Active</option> \
                        <option value="no" \
                        ';
                        if(scope.active == 'no') {
                            options_html += ' selected';
                        }
                        options_html += '>Not Active</option> \
                    </select> \
                </div> \
                ';

            }

            options_html += '</div>';

            if(scope.buttons) {
                scope.buttons.forEach(function(button) {
                    options_html += ' \
                    <div> \
                        <a href="'+button.url+'" target="_blank" class="button primary md">'+button.html+'</a> \
                    </div> \
                    ';
                });
            }

            options_html += '</div>';

            scope.container.querySelector('.options-container').insertAdjacentHTML('beforeend', options_html);

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

        },


    }

}

