if(document.URL.match(/escrow/)) {

    window.escrow = function() {

        return {
            search_val: '',
            active_url: '/transactions_archived/get_escrow_html',
            page_url: '/transactions_archived/get_escrow_html',
            sort: '',
            table: '.escrow-table',
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
