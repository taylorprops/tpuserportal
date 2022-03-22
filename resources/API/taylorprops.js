
load_axios();

window.addEventListener('load', function() {

    let careers_pages = ['careers', '100-commission', '85-commission', 'real-estate-referral'];

    let matched = careers_pages.filter(item => {
        return document.URL.match(item);
    });
    if(matched.length > 0) {

        let get_form_interval = setInterval(() => {

            let form = document.getElementById('iq_lead_form');
            let submit_button = document.getElementById('iq_lead_form-submit');

            if(submit_button) {
                capture_form(form, submit_button);
                clearInterval(get_form_interval);
            }
        }, 2000);

    }

    if(document.URL.match(/utm_source/)) {
        send_lead_to_zoho();
    }

});


function send_lead_to_zoho() {

    // let url = 'https://tpuserportal.com';
    let url = 'https://46d2-71-121-147-194.ngrok.io';

    let utm_source = get_url_parameters('utm_source');
    let utm_medium = get_url_parameters('utm_medium');
    let utm_campaign = get_url_parameters('utm_campaign');
    let email = get_url_parameters('email');

    let formData = new FormData();
    formData.append('utm_source', utm_source);
    formData.append('utm_medium', utm_medium);
    formData.append('utm_campaign', utm_campaign);
    formData.append('email', email);

    axios.post(url+'/api/marketing/add_email_clicker_real_estate', formData)
    .then(function (response) {
    })
    .catch(function (error) {
    });

}

window.capture_form = function(form, submit_button) {

    submit_button.addEventListener('mousedown', function(event) {

        let url = 'https://tpuserportal.com/api/taylor_props/submit_recruiting_form';
        //let url = 'https://862c-71-121-147-194.ngrok.io/api/taylor_props/submit_recruiting_form';

        let first_name = form.querySelector('#iq_lead_form-firstname').value;
        let last_name = form.querySelector('#iq_lead_form-lastname').value;
        let email = form.querySelector('#iq_lead_form-emailaddress').value;
        let phone = form.querySelector('#iq_lead_form-phonenumber').value;
        let message = form.querySelector('textarea').value;

        if(first_name != '' && last_name != '' && email != '' && phone != '' && message != '') {


            let params = {
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone,
                message: message,
            }

            axios.get(url, {
                params: params,
            })
            .then(response => {

            });

        }


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



