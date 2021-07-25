if (document.URL.match(/monitor/)) {

    jobs();
    //setInterval(jobs, 1000);


    function jobs() {
        axios.get('/jobs')
            .then(function (response) {
                document.querySelector('.monitor').innerHTML = response.data;
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

}
