
load_axios();

window.addEventListener('load', function() {

    if(document.URL.match(/utm_source/)) {
        send_lead_to_zoho();
    }

});


function send_lead_to_zoho() {

    let url = 'https://tpuserportal.com';

    let utm_source = get_url_parameters('utm_source');
    let utm_medium = get_url_parameters('utm_medium');
    let utm_campaign = get_url_parameters('utm_campaign');
    let email = get_url_parameters('email');

    let formData = new FormData();
    formData.append('utm_source', utm_source);
    formData.append('utm_medium', utm_medium);
    formData.append('utm_campaign', utm_campaign);
    formData.append('email', email);

    axios.post(url+'/api/marketing/add_email_clicker_title', formData)
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



