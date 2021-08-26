if(document.URL.match(/escrow/)) {

    window.escrow = function() {

        return {
            search_val: '',
            active_url: '',
            init() {
                show_loading();
                this.get_escrow();
            },
            get_escrow(url) {

                let scope = this;
                if(!url) {
                    url = '/transactions_archived/get_escrow_html';
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector('.escrow-table').innerHTML = response.data;
                    scope.links();
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            links() {
                let scope = this;
                document.querySelector('.escrow-table').querySelectorAll('a').forEach(function(link) {
                    if(!link.classList.contains('view-link')) {
                        link.addEventListener('click', function(e) {
                            show_loading();
                            e.preventDefault();
                            let url = this.href;
                            if(this.search_val != '') {
                                if(url.match(/\?/)) {
                                    url += '&';
                                } else {
                                    url += '?';
                                }
                                let params = new URLSearchParams(url.search);
                                params.delete('search');
                                url += 'search=' + scope.search_val;
                            }
                            scope.active_url = url;
                            scope.get_escrow(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active_url = '/transactions_archived/get_escrow_html?search=' + val.trim();
                this.get_escrow(this.active_url);
            }
        }

    }

}
