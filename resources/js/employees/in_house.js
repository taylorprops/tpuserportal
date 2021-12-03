
if(document.URL.match(/in_house$/)) {

    window.employees = function() {

        return {
            search_val: '',
            page_url: '/employees/in_house/get_in_house',
            active_url: '/employees/in_house/get_in_house',
            active: 'yes',
            sort: 'last_name',
            table: '.employees-table',
            init() {
                show_loading();
                table_show_active(this, 'yes');
            },
            init_table_show_active(val) {
                table_show_active(this, val);
            },
            init_table_search(val) {
                table_search(this, val);
            },
            // get_data(url) {

            //     let scope = this;
            //     if(!url) {
            //         url = '/employees/in_house/get_in_house';
            //     }
            //     scope.active_url = url;
            //     axios.get(url)
            //     .then(function (response) {
            //         document.querySelector(scope.table).innerHTML = response.data;
            //         scope.links(scope.table);
            //         hide_loading();
            //     })
            //     .catch(function (error) {
            //         console.log(error);
            //     });

            // },
            // links(table) {
            //     let scope = this;
            //     let container = document.querySelector(table);
            //     let container_table = container.querySelector('table');
            //     container_table.querySelectorAll('th').forEach(function(th) {
            //         if(th.querySelector('a')) {
            //             th.querySelector('a').classList.add('sort-by');
            //         }
            //     });

            //     document.querySelector(table).querySelectorAll('a').forEach(function(link) {
            //         if(!link.classList.contains('view-link')) {
            //             link.addEventListener('click', function(e) {

            //                 show_loading();
            //                 e.preventDefault();

            //                 let url = this.href+'&length='+scope.length;
            //                 let href = new URL(url);
            //                 let params = new URLSearchParams(href.search);

            //                 if(this.search_val != '') {
            //                     if(url.match(/\?/)) {
            //                         url += '&';
            //                     } else {
            //                         url += '?';
            //                     }

            //                     params.delete('search');
            //                     url += 'search=' + scope.search_val;
            //                 }

            //                 if(link.classList.contains('sort-by')) {
            //                     scope.sort = params.get('sort');
            //                 }

            //                 url += '&sort=' + scope.sort;
            //                 scope.active_url = url;
            //                 scope.get_data(url);
            //             });
            //         }
            //     });
            // },
            // search(val) {
            //     this.search_val = val;
            //     this.active = '';
            //     document.querySelector('#active').value = '';
            //     this.active_url = '/employees/in_house/get_in_house?search=' + val.trim();
            //     this.get_data(this.active_url);
            // },
            // show_active(active) {
            //     this.active = active;
            //     this.active_url = this.active_url.replace(/[&]*active=[a-z]*/, '');
            //     if(this.active_url.match(/\?/)) {
            //         this.active_url += '&';
            //     } else {
            //         this.active_url += '?';
            //     }
            //     this.active_url += 'active=' + active;
            //     this.get_data(this.active_url);
            // },
        }


    }


}
