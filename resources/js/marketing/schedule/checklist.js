if (document.URL.match(/checklist/)) {

    window.checklist = function () {

        return {

            active_tab: '0',
            show_add_item_modal: false,

            init() {
                this.get_checklist();
            },

            get_checklist() {
                let scope = this;
                axios.get('/marketing/schedule/get_checklist')
                    .then(function (response) {
                        scope.$refs.checklist_div.innerHTML = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            add_item(company_id, recipient_id) {
                let scope = this;
                scope.show_add_item_modal = true;
            },

        }

    }

}