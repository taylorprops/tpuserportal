if (document.URL.match(/monitor/)) {

    jobs();


    function jobs() {
        axios.get('/jobs')
            .then(function (response) {
                document.querySelector('.monitor').innerHTML = response.data;
                document.querySelector('.text-5xl').classList.add('text-3xl');
                document.querySelector('.text-5xl').classList.remove('text-5xl');

                let insert_after = document.getElementsByClassName('px-6 py-4 mb-6 pl-4');

                let controllers = ' \
                <div class="my-3 block"> \
                    <div class="flex justify-start items-center" x-data="controls()"> \
                        <div class="mr-2"><a href="javascript:void(0)" class="p-2 bg-gray-50 border rounded shadow control-button" x-on:click="play()"><i class="fal fa-play fa-lg text-gray-600"></i></a></div> \
                        <div class="mr-2"><a href="javascript:void(0)" class="p-2 bg-gray-50 border rounded shadow control-button" x-on:click="stop()"><i class="fal fa-stop fa-lg text-gray-600"></i></a></div> \
                        <div class="mr-2"><a href="javascript:void(0)" class="p-2 bg-gray-50 border rounded shadow control-button" x-on:click="refresh()"><i class="fal fa-redo fa-lg text-gray-600"></i></a></div> \
                    </div> \
                </div>';
                let controllers_div = document.createElement('div');
                controllers_div.innerHTML = controllers;
                insertAfter(controllers_div, insert_after.item(0));

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

    function insertAfter(newNode, existingNode) {
        existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    }

}
