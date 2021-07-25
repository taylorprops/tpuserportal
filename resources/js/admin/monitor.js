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
                        <div class="mr-2"> \
                            <a href="javascript:void(0)" class="p-2 border rounded shadow control-button" \
                            x-on:click="active = \'1\'; play()" \
                            x-bind:class="{ \'bg-primary text-white\': active === \'1\', \'bg-gray-50 text-gray-600\': active !== \'1\' }"> \
                                <i class="fal fa-play fa-lg"></i> \
                            </a> \
                        </div> \
                        <div class="mr-2"> \
                            <a href="javascript:void(0)" class="p-2 border rounded shadow control-button" \
                            x-on:click="active = \'2\'; stop()" \
                            x-bind:class="{ \'bg-primary text-white\': active === \'2\', \'bg-gray-50 text-gray-600\': active !== \'2\' }"> \
                                <i class="fal fa-stop fa-lg"></i> \
                            </a> \
                        </div> \
                        <div class="mr-2"> \
                            <a href="javascript:void(0)" class="p-2 border rounded shadow control-button" \
                            x-on:click="active = \'3\'; refresh()" \
                            x-bind:class="{ \'bg-primary text-white\': active === \'3\', \'bg-gray-50 text-gray-600\': active !== \'3\' }"> \
                                <i class="fal fa-redo fa-lg"></i> \
                            </a> \
                        </div> \
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
