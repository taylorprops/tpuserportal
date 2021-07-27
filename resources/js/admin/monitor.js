if (document.URL.match(/monitor/)) {

    jobs();


    function jobs() {
        let page = global_get_url_parameters('page') || '';
        let type = global_get_url_parameters('type') || '';
        let queue = global_get_url_parameters('queue') || '';

        axios.get('/jobs?type='+type+'&queue='+queue+'&page='+page)
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
            play_interval: null,
            active: '0',
            play() {
                if(this.play_interval) {
                    clearInterval(this.play_interval);
                }
                this.play_interval = setInterval(jobs, 1000);
            },
            stop() {
                clearInterval(this.play_interval);
            },
            refresh() {
                jobs();
            }
        }
    }

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

}
