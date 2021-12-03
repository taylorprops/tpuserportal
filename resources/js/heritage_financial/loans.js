if(document.URL.match(/loans/)) {


    window.loans = function() {

        return {

            search_val: '',
            active_url: '/heritage_financial/loans/get_loans',
            page_url: '/heritage_financial/loans/get_loans',
            length: '10',
            sort: 'settlement_date',
            table: '.loans-table',
            init() {
                show_loading();
                table_show_active(this, 'yes');
            },
            init_table_change_length(val) {
                table_change_length(scope, val);
            },
            init_table_show_active(val) {
                table_show_active(this, val);
            },
            init_table_search(val) {
                table_search(this, val);
            },

        }

    }

}
