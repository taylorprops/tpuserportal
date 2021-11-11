if(document.URL.match(/archived/)) {

    window.archives = function() {

        return {
            search_val: '',
            active_url: '',
            sort: 'actualClosingDate',
            table: '.archives-table',
            init() {
                show_loading();
                this.get_data();
            },
            get_data(url) {

                let scope = this;
                if(!url) {
                    url = '/get_transactions_archived';
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector(scope.table).innerHTML = response.data;
                    scope.links(scope.table);
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            links(table) {
                let scope = this;
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

                            let url = this.href+'&length='+scope.length;
                            let href = new URL(url);
                            let params = new URLSearchParams(href.search);

                            if(this.search_val != '') {
                                if(url.match(/\?/)) {
                                    url += '&';
                                } else {
                                    url += '?';
                                }

                                params.delete('search');
                                url += 'search=' + scope.search_val;
                            }

                            if(link.classList.contains('sort-by')) {
                                scope.sort = params.get('sort');
                            }

                            url += '&sort=' + scope.sort;
                            scope.active_url = url;
                            scope.get_data(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active_url = '/get_transactions_archived?search=' + val.trim();
                this.get_data(this.active_url);
            }
        }

    }

}
