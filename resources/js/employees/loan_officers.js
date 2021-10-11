
if(document.URL.match(/loan_officer$/)) {


    window.employees = function() {

        return {
            search_val: '',
            active_url: '/employees/loan_officer/get_loan_officers',
            active: 'yes',
            init() {
                show_loading();
                this.show_active('yes');
            },
            get_employees(url) {

                let scope = this;
                if(!url) {
                    url = '/employees/loan_officer/get_loan_officers';
                }
                scope.active_url = url;
                axios.get(url)
                .then(function (response) {
                    document.querySelector('.employees-table').innerHTML = response.data;
                    scope.links();
                    hide_loading();
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            links() {
                let scope = this;
                document.querySelector('.employees-table').querySelectorAll('a').forEach(function(link) {
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
                            scope.get_employees(url);
                        });
                    }
                });
            },
            search(val) {
                this.search_val = val;
                this.active = '';
                document.querySelector('#active').value = '';
                this.active_url = '/employees/loan_officer/get_loan_officers?search=' + val.trim();
                this.get_employees(this.active_url);
            },
            show_active(active) {
                this.active = active;
                this.active_url = this.active_url.replace(/[&]*active=[a-z]*/, '');
                if(this.active_url.match(/\?/)) {
                    this.active_url += '&';
                } else {
                    this.active_url += '?';
                }
                this.active_url += 'active=' + active;
                this.get_employees(this.active_url);
            },
        }


    }


}
