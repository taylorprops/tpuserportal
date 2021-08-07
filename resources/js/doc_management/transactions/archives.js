if(document.URL.match(/archived/)) {

    window.archives = function() {

        return {
            search_val: '',
            active_url: '',
            init() {
                this.get_archives();
            },
            get_archives(url) {

                let scope = this;
                if(!url) {
                    url = '/get_transactions_archived';
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector('.archives-table').innerHTML = response.data;
                    scope.links();
                })
                .catch(function (error) {
                    console.log(error);
                });

                // show_loading();
                // hide_loading();

            },
            links() {
                let scope = this;
                document.querySelector('.archives-table').querySelectorAll('a').forEach(function(link) {
                    if(!link.classList.contains('view-link')) {
                        link.addEventListener('click', function(e) {
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
                            scope.get_archives(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active_url = '/get_transactions_archived?search=' + val.trim();
                this.get_archives(this.active_url);
            }
        }

    }

}
