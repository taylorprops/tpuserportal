if(document.URL.match(/commission_reports/)) {

    window.commission_reports = function() {

        return {
            search_val: '',
            active_url: '/heritage_financial/loans/get_commission_reports',
            page_url: '/heritage_financial/loans/get_commission_reports',
            sort: 'borrower_last',
            table: '.commission-reports-table',
            length: '10',
            init() {
                show_loading();
                table_init(this, this.active_url);
            },
            init_table_change_length(val) {
                table_change_length(this, val);
            },
            init_table_search(val) {
                table_search(this, val);
            },
        }

    }

}
