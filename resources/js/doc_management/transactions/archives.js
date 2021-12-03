if(document.URL.match(/archived/)) {

    window.archives = function() {

        return {
            search_val: '',
            active_url: '',
            sort: 'actualClosingDate',
            table: '.archives-table',

            search_val: '',
            active_url: '/get_transactions_archived',
            page_url: '/get_transactions_archived',
            sort: 'actualClosingDate',
            table: '.archives-table',
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
