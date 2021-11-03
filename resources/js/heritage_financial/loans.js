if(document.URL.match(/loans/)) {


    window.loans = function() {

        return {

            search_val: '',
            active_url: '',
            length: '10',
            init() {
                this.get_loans();
            },
            get_loans(url = null) {

                let scope = this;
                if(!url) {
                    url = '/heritage_financial/loans/get_loans';
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector('.loans-table').innerHTML = response.data;
                    scope.links();
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            links() {
                let scope = this;
                document.querySelector('.loans-table').querySelectorAll('a').forEach(function(link) {
                    if(!link.classList.contains('view-link')) {
                        link.addEventListener('click', function(e) {
                            show_loading();
                            e.preventDefault();
                            let url = this.href+'&length='+scope.length;
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
                            scope.get_loans(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active_url = '/heritage_financial/loans/get_loans?search=' + val.trim()+'&length='+this.length;
                this.get_loans(this.active_url);
            },
            change_length(ele) {
                let val = parseInt(ele.value);
                this.length = val;
                this.active_url = '/heritage_financial/loans/get_loans?length=' + val;
                this.get_loans(this.active_url);
            }

        }

    }

}
