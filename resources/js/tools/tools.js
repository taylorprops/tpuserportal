if (document.URL.match(/tools/)) {

    window.tools = function () {

        return {

            init() {

            },

            create_classes(ele, type) {

                let scope = this;

                let style = scope.$refs.style.value;
                let level = scope.$refs.level.value;
                let single = scope.$refs.single.value;

                let button_html = ele.innerHTML;
                show_loading_button(ele, 'Saving ... ');

                let formData = new FormData();
                formData.append('type', type);
                formData.append('style', style);
                formData.append('level', level);
                formData.append('single', single);

                axios.post('/tools/create_classes', formData)
                    .then(function (response) {
                        ele.innerHTML = button_html;
                        scope.$refs.classes_div.innerHTML = response.data;

                    })
                    .catch(function (error) {
                        display_errors(error, ele, button_html);
                    });

            },

        }

    }

}