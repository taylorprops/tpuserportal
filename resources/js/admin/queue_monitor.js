if (document.URL.match(/monitor/)) {

    jobs();


    function jobs() {
        let page = global_get_url_parameters('page') || '';
        let type = global_get_url_parameters('type') || '';
        let queue = global_get_url_parameters('queue') || '';

        axios.get('/jobs?type='+type+'&queue='+queue+'&page='+page)
        .then(function (response) {

            document.querySelector('.monitor').innerHTML = response.data;

            let monitor = document.querySelector('.monitor');
            //console.log(monitor.offsetLeft);
            monitor.querySelector('[charset="utf-8"]').remove();
            monitor.querySelector('[name="viewport"]').remove();
            monitor.querySelector('title').remove();
            let link = monitor.getElementsByTagName('link')[0];
            link.remove();
            let h1 = monitor.getElementsByTagName('h1')[0];
            h1.remove();

            // document.querySelector('.monitor').innerHTML = '';
            // document.querySelector('.monitor').insertAdjacentHTML('beforeend', response.data);

        })
        .catch(function (error) {
            console.log(error);
        });
    }


    window.controls = function() {
        return {
            play_interval: null,
            active: '0',
            init() {
                let scope = this;
                setTimeout(function() {
                    scope.resize_textareas('16');
                }, 1000);
            },
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
            },
            resize_textareas(rows) {
                document.querySelectorAll('.monitor textarea').forEach(function (textarea) {
                    textarea.setAttribute('rows', rows);
                });
            }
        }
    }

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

}
