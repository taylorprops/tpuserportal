if(document.URL.match(/manager_bonuses/)) {

    window.bonuses = function() {

        return {

            show_email_bonuses: false,
            email_ele: null,

            print(ele) {
                let print_page = window.open('');
                ele.querySelector('table').setAttribute('border', '1');
                ele.querySelector('table').setAttribute('cellpadding', '6');
                ele.querySelector('table').style.fontSize = '10px';
                ele.querySelector('table').style.fontFamily = 'Arial';
                print_page.document.write(ele.innerHTML);
                print_page.stop();
                print_page.print();
                print_page.close();
            },

            send_email(button) {

                let button_html = button.innerHTML;
                show_loading_button(button, 'Sending Email ... ');

                let scope = this;
                ele = scope.email_ele;
                ele.querySelector('table').setAttribute('border', '1');
                ele.querySelector('table').setAttribute('cellpadding', '6');
                ele.querySelector('table').style.fontSize = '12px';
                ele.querySelector('table').style.fontFamily = 'Arial';
                let html = ele.innerHTML;
                let to_email = scope.$refs.to_email.value;

                let formData = new FormData();
                formData.append('html', html);
                formData.append('to_email', to_email);

                axios.post('/heritage_financial/email_manager_bonuses', formData)
                .then(function (response) {
                    toastr.success('Email successfully sent');
                    scope.show_email_bonuses = false;
                    button.innerHTML = button_html;
                })
                .catch(function (error) {
                });
            },

            toggle_link(ele, type, year) {

                document.querySelectorAll('.year-container').forEach(function(container) {

                    let year_icon = container.querySelector('.year-icon');
                    let container_year = container.getAttribute('data-year');

                    if(container_year == year) {
                        if(year_icon.classList.contains('rotate-90')) {
                            year_icon.classList.remove('rotate-90');
                        } else {
                            year_icon.classList.add('rotate-90');
                        }
                    } else {
                        year_icon.classList.remove('rotate-90');
                    }
                });

            },

        }

    }

}








