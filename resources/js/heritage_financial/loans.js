if(document.URL.match(/loans/)) {


    window.loans = function() {

        return {

            search_val: '',
            active_url: '',
            length: '10',
            sort: 'settlement_date',
            table: '.loans-table',
            init() {
                this.get_data();
            },
            get_data(url = null) {

                let scope = this;
                if(!url) {
                    url = '/heritage_financial/loans/get_loans';
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
                this.active_url = '/heritage_financial/loans/get_loans?search=' + val.trim()+'&length='+this.length;
                this.get_data(this.active_url);
            },
            change_length(ele) {
                let val = parseInt(ele.value);
                this.length = val;
                this.active_url = '/heritage_financial/loans/get_loans?length=' + val;
                this.get_data(this.active_url);
            }

        }

    }

}
