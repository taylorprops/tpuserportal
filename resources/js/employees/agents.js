
if(document.URL.match(/agents$/)) {


    window.agents = function() {

        return {
            init() {
                this.get_agents();
            },
            get_agents() {

                show_loading();

                let cols = [
                    { data: 'edit', orderable: false, searchable: false },
                    { data: 'full_name' },
                    { data: 'cell_phone' },
                    { data: 'email' }
                ];
                let table = document.querySelector('#agents_table');
                // TODO: remove datatables
                data_table('/employees/agents/get_agents', cols, 25, $(table), [1, 'asc'], [0], [], true, true, true, true, true);

                table.classList.remove('hidden');
                hide_loading();

            }
        }

    }


}
