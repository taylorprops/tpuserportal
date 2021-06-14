
if(document.URL.match(/agents$/)) {

    window.addEventListener('load', (event) => {

        show_loader();
        get_agents();

    });

    function get_agents() {

        let cols = [
            { data: 'edit', orderable: false, searchable: false },
            { data: 'full_name' },
            { data: 'cell_phone' },
            { data: 'email' }
        ];

        data_table('/employees/agents/get_agents', cols, 25, $('#agents_table'), [1, 'asc'], [0], [], true, true, true, true, true);

        $('#agents_table').show();
        hide_loader();


        /* axios.get('/employees/agents/get_agents', {
            params: {

            },
        })
        .then(function (response) {

            $('#agents_table').show().find('tbody').html(response.data);
            hide_loader();
            data_table('/employees/agents/get_agents', 25, $('#agents_table'), [1, 'asc'], [0], [], true, true, true, true, true);
        })
        .catch(function (error) {
            console.log(error);
        }); */

    }

}
