if(document.URL.match(/tests/)) {


    window.field_buttons = function() {
        return {
            active(ele) {
                buttons = document.querySelectorAll('button');
                buttons.forEach(function(button) {
                    button.classList.remove('bg-secondary', 'border-secondary');
                });
                ele.classList.add('bg-secondary', 'border-secondary');
            }
        }
    }

}
