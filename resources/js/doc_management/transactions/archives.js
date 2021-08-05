if(document.URL.match(/archived/)) {

    window.archives = function() {

        return {
            init() {
                this.get_archives();
            },
            get_archives() {

                show_loading();

                let cols = [
                    { data: 'edit', orderable: false, searchable: false },
                    { data: 'name' }
                ];
                let table = document.querySelector('#archives_table');

                data_table('/get_transactions_archived', cols, 25, $(table), [1, 'asc'], [0], [], true, true, true, true, true);

                table.classList.remove('hidden');
                hide_loading();

            }
        }

    }

}
