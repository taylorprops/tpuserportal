if(document.URL.match('marketing/schedule')) {

    window.schedule = function() {

        return {

            show_add_item_modal:false,

            init() {
                this.get_schedule();
            },

            get_schedule() {
                let scope = this;
                axios.get('/marketing/get_schedule')
                .then(function (response) {
                    scope.$refs.schedule_list_div.innerHTML = response;
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        }

    }

}
