if (document.URL.match(/monitor/)) {

    jobs();


    function jobs() {
        let type = global_get_url_parameters('type') || '';
        let queue = global_get_url_parameters('queue') || '';

        axios.get('/jobs?type='+type+'&queue='+queue)
        .then(function (response) {
            document.querySelector('.monitor').innerHTML = response.data;
            // document.querySelector('.monitor').innerHTML = '';
            // document.querySelector('.monitor').insertAdjacentHTML('beforeend', response.data);
            document.querySelector('.text-5xl').classList.add('text-3xl');
            document.querySelector('.text-5xl').classList.remove('text-5xl');

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.controls = function() {
        return {
            play_interval: '',
            active: '0',
            play() {
                play_interval = setInterval(jobs, 1000);
            },
            stop() {
                clearInterval(play_interval);
            },
            refresh() {
                clearInterval(play_interval);
                jobs();
            }
        }
    }

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

}
