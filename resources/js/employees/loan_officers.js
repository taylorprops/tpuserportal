
if(document.URL.match(/loan_officer$/)) {


    window.employees = function() {

        return {
            search_val: '',
            active_url: '/employees/loan_officer/get_loan_officers',
            page_url: '/employees/loan_officer/get_loan_officers',
            active: 'yes',
            sort: 'last_name',
            table: '.employees-table',
            length: '10',
            init() {
                show_loading();
                table_show_active(this, 'yes');
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
