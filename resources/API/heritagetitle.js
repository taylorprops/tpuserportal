
load_axios();

let domain = 'https://tpuserportal.com';
// let domain = 'https://4c4d-71-121-147-194.ngrok.io';

window.addEventListener('load', function() {

    if(document.URL.match(/utm_source/)) {
        send_lead_to_zoho();
    }

    let contact_form = document.querySelector('#submit_form');
    if(contact_form) {
        document.querySelector('#submit_form').addEventListener('mousedown', function(e) {
            let form = document.querySelector('#contact_form');
            e.preventDefault();
            capture_form(form);
        });
    }

    // document.querySelectorAll('a', 'link').forEach(function(link) {
    //     let href = link.getAttribute('href').replace('titlemd', 'title');
    //     link.setAttribute('href', href);
    // });

});

function capture_form(form) {

    url = domain+'/api/heritage_title/submit_contact_form_title';

    let name = form.querySelector('#form-field-name').value;
    let email = form.querySelector('#form-field-email').value;
    let phone = form.querySelector('#form-field-phone').value;
    let message = form.querySelector('#form-field-message').value;

    let formData = new FormData();
    formData.append('full_name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('message', message);

    axios.post(url, formData)
    .then(function (response) {
        form.submit();
    })
    .catch(function (error) {
    });


}


function send_lead_to_zoho() {



    let utm_source = get_url_parameters('utm_source');
    let utm_medium = get_url_parameters('utm_medium');
    let utm_campaign = get_url_parameters('utm_campaign');
    let email = get_url_parameters('email');

    let formData = new FormData();
    formData.append('utm_source', utm_source);
    formData.append('utm_medium', utm_medium);
    formData.append('utm_campaign', utm_campaign);
    formData.append('email', email);

    axios.post(domain+'/api/marketing/add_email_clicker_title', formData)
    .then(function (response) {
    })
    .catch(function (error) {
    });

}

function get_url_parameters(key) {
    // usage
    // let tab = global_get_url_parameters('tab');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(key)) {
        return urlParams.get(key);
    }
    return false;
}

function load_axios() {
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';
    document.head.appendChild(script);
}



