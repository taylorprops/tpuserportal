import Toastr from 'toastr2';
window.toastr = new Toastr();

toastr.options.preventDuplicates = true;

window.addEventListener('load', (event) => {
});

window._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// axios headersObj
window.axios_options = {
    headers: { 'X-CSRF-TOKEN': _token }
};


// Add a response interceptor

/* axios.interceptors.response.use(function (response) {

    if(response.data.message) {
        if(response.data.message.match(/Unauthenticated/)) {
            window.location.href = '/login';
        }
    }
    return response;

}, function (error) {
    console.log(error);
    if(error.data.message) {
        if(error.data.message.match(/Unauthenticated/)) {
            window.location.href = '/login';
        }
    }

}); */


window.show_loader = function() {
    document.querySelector('body').__x.$data.show_loading = true;
}
window.hide_loader = function() {
    document.querySelector('body').__x.$data.show_loading = false;
}



window.show_form_errors = function(errors) {
    Object.entries(errors).forEach(([key, value]) => {
        let field = `${key}`;
        let message = `${value}`;
        let element = document.querySelector('#'+field);
        if(element) {
            let error_message = element.closest('label').querySelector('.error-message');
            error_message.innerHTML = message;
        }

    });
}

window.remove_form_errors = function(event = null) {

    if(event) {
        let label = event.target.closest('label');
        label.querySelector('.error-message').innerHTML = '';
        label.querySelector('.error-message').classList.toggle('hidden');
    } else {
        document.querySelectorAll('.error-message').forEach(function(error_div) {
            error_div.innerHTML = '';
        });
    }
}


window.show_loading_button = function(button, text) {
    button.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> '+text;
}

window.decode_HTML = function (html) {
	var txt = document.createElement('textarea');
	txt.innerHTML = html;
	return txt.value;
};
