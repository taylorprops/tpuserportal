if(document.URL.match(/transactions\/create/)) {

    window.addEventListener('load', (event) => {

        document.getElementById('address_search_input').focus();

    });

    window.create = function(type) {

        return {
            transaction_type: '',
            search_type: 'address',
            active_step: '1',
            steps_complete: '0',
            address_search_continue: false,
            show_street_error: false,
            show_license_state_error: false,
            show_multiple_error: false,
            address_search() {

                let scope = this;
                // search input
                let address_search_street = document.getElementById('address_search_input');

                // google address search
                let places = new google.maps.places.Autocomplete(address_search_street);
                google.maps.event.addListener(places, 'place_changed', function () {

                    let address_details = places.getPlace();
                    let street_number = street_name = city = county = state = zip = '', county = '';

                    scope.show_license_state_error = false;

                    sessionStorage.clear();

                    address_details.address_components.forEach(function (address) {

                        if (address.types.includes('street_number')) {
                            street_number = address.long_name;
                        } else if (address.types.includes('route')) {
                            street_name = address.long_name;
                        } else if (address.types.includes('locality')) {
                            city = address.long_name;
                        } else if (address.types.includes('administrative_area_level_2')) {
                            county = address.long_name.replace(/'/, '');
                            county = county.replace(/\sCounty/, '');
                        } else if (address.types.includes('administrative_area_level_1')) {
                            state = address.short_name;
                        } else if (address.types.includes('postal_code')) {
                            zip = address.long_name;
                        }

                        let search_details = {
                            'street_number': street_number,
                            'street_name': street_name,
                            'city': city,
                            'state': state,
                            'zip': zip,
                            'county': county
                        };
                        let property_details = {
                            'prop_street_number': street_number,
                            'prop_street_name': street_name,
                            'prop_city': city,
                            'prop_state': state,
                            'prop_zip': zip,
                            'prop_county': county
                        };

                        sessionStorage.setItem('search_details', JSON.stringify(search_details));
                        sessionStorage.setItem('property_details', JSON.stringify(property_details));
                        // console.log(JSON.parse(sessionStorage.search_details));
                        // console.log(JSON.parse(sessionStorage.property_details));
                    });

                    if (street_number != '') {
                        scope.address_search_continue = true;
                        scope.show_street_error = false;
                    } else {
                        scope.address_search_continue = false;
                        scope.show_street_error = true;
                    }

                });
            },
            get_property_info(ele, search_type) {

                let ListingId = document.getElementById('mls_search_input').value;

                let scope = this;
                let search_details = JSON.parse(sessionStorage.search_details);
                let street_number = search_details.street_number;
                let street_name = search_details.street_name;
                let city = search_details.city;
                let state = search_details.state;
                let zip = search_details.zip;
                let county = search_details.county;
                let unit_number = document.getElementById('address_search_unit').value;

                scope.show_multiple_error = false;

                // set unit number - not always added until "Continue" clicked
                search_details.unit_number = unit_number;
                search_details.ListingId = ListingId;

                sessionStorage.search_details = JSON.stringify(search_details);

                let address = street_number+' '+street_name;
                if(search_details.unit_number != '') {
                    address += ' #'+search_details.unit_number;
                }
                address += ' '+city+' '+state+' '+zip;
                document.querySelectorAll('.address-header').forEach(function(ele) {
                    ele.innerHTML = address;
                });

                if(this.transaction_type == 'referral') {
                    this.active_step = '3';
                    return false;

                }


                // if not a referral

                let active_states = document.getElementById('global_company_active_states').value.split(',');
                if(active_states.includes(state) == false) {
                    this.show_license_state_error = true;
                    return false;
                }

                show_loading_button(ele, 'Searching...');
                ele.disabled = true;
                let search_active = true;

                axios.get('/transactions/get_property_info', {
                    params: {
                        ListingId: ListingId,
                        transaction_type: this.transaction_type,
                        search_type: this.search_type,
                        street_number: street_number,
                        street_name: street_name,
                        unit: unit_number,
                        city: city,
                        state: state,
                        zip: zip,
                        county: county
                    },
                })
                .then(function (response) {

                    let property_details = response.data.property_details;

                    ele.innerHTML = 'Continue <i class="fal fa-arrow-right ml-2"></i>';
                    ele.disabled = false;
                    search_active = false;

                    if(response.data.results.multiple == true) {

                        scope.show_multiple_error = true;

                        let list = document.querySelector('.multiple-results-list');
                        list.innerHTML = '';

                        let  html = '';
                        property_details.forEach(function(property) {
                            html += ' \
                            <li class="text-sm p-2 border-b text-gray-600 flex justify-around items-center"> \
                                <div class="w-24"> \
                                    <button class="text-xs px-2 py-1 bg-primary text-white rounded" \
                                    @click="document.getElementById(\'address_search_unit\').value = \''+property.UnitNumber+'\'; document.querySelector(\'.get-property-info\').click()"> \
                                    <i class="fal fa-check mr-2"></i> Select \
                                    </button> \
                                </div> \
                                <div class="flex-grow">'+property.FullStreetAddress+' '+property.City+', '+property.StateOrProvince+' '+property.PostalCode+'</div> \
                                <div class="w-24">'+property.UnitNumber+'</div> \
                            </li>';

                        });

                        list.innerHTML += html;

                    } else {

                        if(property_details) {
                            // show result

                            // add to sessionStorage

                        } else {

                            // show no results

                        }

                    }



                    // go to step 2


                })
                .catch(function (error) {
                    console.log(error);
                });

                // if no results after 3 seconds resend the request
                setTimeout(function() {
                    if(search_active == true) {
                        ele.disabled = false;
                        ele.click();
                    }
                }, 3000);

            }
        }

    }

}
