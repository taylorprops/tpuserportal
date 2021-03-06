

window.table = function(options) {

    let container = options.container;
    let data_url = options.data_url;
    let dates = options.dates || null;
    let dates_col = null;
    if(options.dates) {
        dates_col = options.dates.col;
    }
    let sort = options.sort_by || null;
    let length = options.length || null;
    let button_export = options.button_export || false;
    let export_url = options.export_url || null;
    let buttons = options.buttons || null;
    let search = options.search == false ? false : true;
    let fields = options.fields || null;
    let additional_html = options.additional_html || null;
    let form_id = options.form_id || null;

    // XXXXXXXX Create refresh function

    return {

        container: container,
        search: search,
        search_val: '',
        active_url: data_url,
        page_url: data_url,
        button_export: button_export,
        export_url: export_url,
        buttons: buttons,
        fields: fields,
        dates: dates,
        date_col: dates_col,
        start_date: null,
        end_date: null,
        additional_html: additional_html,
        option_db_fields: [],
        sort: sort,
        direction: 'asc',
        length: length,
        page: '1',
        form_id: form_id,

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

        load_table(url = null, to_excel = false) {

            let scope = this;

            if(!url) {
                url = scope.page_url+'?length='+scope.length;
            }

            if(scope.form_id) {

                let form = document.getElementById(scope.form_id);

                [...form.elements].forEach((item) => {
                    if(item.getAttribute('name')) {
                        url += '&'+item.getAttribute('name')+'='+item.value;
                    }
                });

            }

            scope.active_url = url;

            axios.get(url, {
                params: {
                    to_excel: to_excel
                },
            })
            .then(function (response) {
                if(to_excel == false) {
                    let html = ' \
                    <div class="flex flex-col"> \
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"> \
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"> \
                                '+response.data+' \
                            </div> \
                        </div> \
                    </div> \
                    ';
                    scope.container.querySelector('.table-container').innerHTML = html;
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

            let options_html = ' \
            <div class="mb-8"> \
                <div class="flex justify-between options-div"> \
                    <div class="flex">';

            if(scope.search == true) {

                        options_html += ' \
                        <div class="w-36"> \
                            <input \
                            type="text" \
                            class="form-element input md" \
                                data-label="Search" \
                                x-on:keyup="option_search($el.value)"> \
                        </div> \
                        ';

            }

            if(scope.dates) {

                        options_html += ' \
                        <div class="ml-6"> \
                            <div class="flex items-end justify-start space-x-4"> \
                                <div> \
                                    <input \
                                    type="date" \
                                    class="form-element input md" \
                                        data-label="'+scope.dates.text+'" \
                                        placeholder="Start Date" \
                                        x-ref="start_date" \
                                        x-on:change="option_dates()"> \
                                </div> \
                                <div> to </div> \
                                <div> \
                                    <input \
                                    type="date" \
                                    class="form-element input md" \
                                        data-label="" \
                                        placeholder="End Date" \
                                        x-ref="end_date" \
                                        x-on:change="option_dates()"> \
                                </div> \
                            </div> \
                        </div> \
                        ';

            }

            if(scope.fields) {

                Object.entries(scope.fields).forEach(([key, option]) => {

                    let field_type = option.type;
                    let db_field = option.db_field;
                    let label = option.label;
                    let options = option.options;
                    let value = option.value;

                    if(field_type === 'select') {
                        options_html += ' \
                        <div class="ml-6 w-auto"> \
                            <select \
                            class="form-element select md" \
                            data-label="'+label+'" \
                            x-on:change="option_db_field(\''+db_field+'\', $el.value);">';
                            Object.entries(options).forEach(([key, option]) => {
                                let selected = option[0] == value ? 'selected' : '';
                                options_html += '<option value="'+option[0]+'" '+selected+'>'+option[1]+'</option>';
                            });
                            options_html += '</select> \
                        </div>';
                    }
                });


            }

            options_html += ' \
                    </div>';

            if(scope.buttons) {
                scope.buttons.forEach(function(button) {
                    options_html += ' \
                    <div> \
                        <a href="'+button.url+'" target="_blank" class="button primary md">'+button.html+'</a> \
                    </div> \
                    ';
                });
            }

            options_html += ' \
                </div>';

            if(scope.additional_html) {

                options_html += ' \
                <div class="ml-8 my-8"> \
                    '+scope.additional_html+' \
                </div>';

            }

            options_html += ' \
            </div>';

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
                        sort, direction, page, length, search, option_db_fields
                    */

                    if(link.classList.contains('sort-by')) {
                        // leave out sort and direction because they are in the link already
                        scope.sort = params.get('sort');
                        scope.direction = params.get('direction');
                        url += '&search='+scope.search_val+'&length='+scope.length+'&page='+scope.page;

                    } else {

                        scope.page = params.get('page');
                        url += '&search='+scope.search_val+'&length='+scope.length+'&sort='+scope.sort+'&direction='+scope.direction;

                    }
                    scope.option_db_fields.forEach(function(field) {
                        url += '&'+field.db_field+'='+field.value;
                    });

                    scope.active_url = url;
                    scope.load_table(url);

                });

            });

        },

        option_db_field(db_field, value) {

            let scope = this;

            let field = {
                db_field: db_field,
                value: value
            }

            let option_added = false;
            this.option_db_fields.forEach(function(field) {
                if(field.db_field === db_field) {
                    field.value = value;
                    option_added = true;
                }
            });
            if(option_added === false) {
                this.option_db_fields.push(field);
            }

            this.option_db_fields.forEach(function(field) {
                scope.add_url_param(field.db_field, field.value);
            });

            scope.load_table(scope.active_url);

        },

        option_search(val) {

            let scope = this;

            scope.search_val = val;

            let url = scope.add_url_param('search', val.trim());

            scope.load_table(url);

        },

        table_change_length(val) {

            let scope = this;

            length = parseInt(val);
            scope.length = length;

            let url = scope.add_url_param('length', length);

            scope.load_table(url);

        },

        option_dates() {

            let scope = this;
            let start_date = scope.$refs.start_date.value;
            let end_date = scope.$refs.end_date.value;

            scope.start_date = start_date;
            scope.end_date = end_date;

            scope.add_url_param('date_col', scope.date_col);
            scope.add_url_param('start_date', start_date);
            scope.add_url_param('end_date', end_date);

            setTimeout(function() {
                scope.load_table(scope.active_url);
            }, 100);
        },

        add_url_param(key, val) {

            let scope = this;

            let url = scope.page_url+'?search='+scope.search_val+'&length='+scope.length+'&sort='+scope.sort+'&direction='+scope.direction+'&start_date='+scope.start_date+'&end_date='+scope.end_date+'&date_col='+scope.date_col;

            this.option_db_fields.forEach(function(field) {
                url += '&'+field.db_field+'='+field.value;
            });

            let param = new RegExp('[&]*'+key+'=[a-zA-Z0-9_-]*');
            url = url.replace(param, '');
            url += '&'+key+'='+val;

            scope.active_url = url;

            return url;

        },


    }

}






