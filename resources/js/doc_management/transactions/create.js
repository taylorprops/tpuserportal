if(document.URL.match(/transactions\/create/)) {

    window.addEventListener('load', (event) => {

        document.getElementById('address_search_input').focus();

        sessionStorage.clear();
        sessionStorage.search_details = '';

        // setInterval(function() {
        //     console.log(sessionStorage.property_details);
        // }, 2000);

    });

    window.create = function(type) {

        return {
            transaction_type: '',
            search_type: 'address',
            active_step: 1,
            steps_complete: '0',
            show_no_property_error: false,
            show_street_error: false,
            show_license_state_error: false,
            final_result: false,
            property_found_mls: false,
            property_found_tax_records: false,
            tax_records_link: false,
            counties: [],
            property_types: [],
            property_sub_types: [],
            property_type: '',
            address_not_found: false,
            show_both: true,
            for_sale: 'yes',
            show_disclosures: true,
            show_add_contact_modal: false,
            seller_is_trust: false,
            buyer_is_trust: false,
            import_contact_member_id: 1,
            show_agent_search_results: false,
            using_heritage_title: false,
            address_search() {

                let scope = this;
                // search input
                let address_search_street = document.getElementById('address_search_input');

                // google address search
                let places = new google.maps.places.Autocomplete(address_search_street);
                google.maps.event.addListener(places, 'place_changed', function () {

                    let address_details = places.getPlace();
                    let street_number = street_name = city = county = state = zip = '', county = '';


                    sessionStorage.clear();
                    // sessionStorage.search_count = 0;

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

                    });

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

                    document.getElementById('street_number').value = street_number;
                    document.getElementById('street_name').value = street_name;
                    document.getElementById('zip').value = zip;
                    document.getElementById('city').value = city;
                    document.getElementById('state').value = state;

                    if(county && county != '') {

                        let event = new Event('change');
                        document.getElementById('state').dispatchEvent(event);
                        setTimeout(function() {
                            document.getElementById('county').value = county;
                        }, 300);

                        if (street_number != '') {
                            scope.show_street_error = false;
                        } else {
                            scope.show_street_error = true;
                        }

                    } else {

                        scope.get_location_details('#manual_entry_form', '', document.getElementById('zip'), '#city', '#state', '#county');

                    }

                });
            },
            get_property_info(ele, search_type) {

                let scope = this;
                let ListingId = street_number = street_name = unit_number = city = state = zip = county = '';

                scope.final_result = false;
                scope.show_license_state_error = false;
                scope.show_no_property_error = false;
                scope.tax_records_link = false;

                let mls_search = null;

                if(ele.classList.contains('address-search')) {

                    if(document.getElementById('address_search_input').value == '') {
                        return false;
                    }

                    let search_details = JSON.parse(sessionStorage.search_details);
                    street_number = search_details.street_number;
                    street_name = search_details.street_name;
                    city = search_details.city;
                    state = search_details.state;
                    zip = search_details.zip;
                    county = search_details.county;
                    unit_number = document.getElementById('address_search_unit').value;

                    // set unit number - not always added until "Continue" clicked
                    search_details.unit_number = unit_number;

                    sessionStorage.search_details = JSON.stringify(search_details);

                    document.getElementById('street_number').value = street_number;
                    document.getElementById('street_name').value = street_name;
                    document.getElementById('unit').value = unit_number;
                    document.getElementById('zip').value = zip;
                    document.getElementById('city').value = city;
                    document.getElementById('state').value = state;
                    let event = new Event('change');
                    document.getElementById('state').dispatchEvent(event);
                    setTimeout(function() {
                        document.getElementById('county').value = county;
                    }, 100);

                    this.address_header(street_number, street_name, unit_number, city, state, zip);

                    let active_states = document.getElementById('global_company_active_states').value.split(',');
                    if(active_states.includes(state) == false) {
                        this.show_license_state_error = true;
                        return false;
                    }

                } else if(ele.classList.contains('mls-search')) {

                    mls_search = 'yes';
                    if(document.getElementById('mls_search_input').value == '') {
                        return false;
                    }
                    ListingId = document.getElementById('mls_search_input').value;

                }

                if(this.transaction_type == 'referral') {
                    this.active_step = 3;
                    return false;

                }



                show_loading_button(ele, 'Searching...');

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

                    if(mls_search) {
                        if(response.data.error && response.data.error == 'not found') {
                            scope.show_no_property_error = true;
                            ele.innerHTML = 'Continue <i class="fal fa-arrow-right ml-2"></i>';
                            return false;
                        }
                    }

                    let property_details = response.data.property_details;

                    ele.innerHTML = 'Continue <i class="fal fa-arrow-right ml-2"></i>';


                    if(property_details) {
                        // show result

                        document.querySelector('#property_address').innerHTML = property_details.FullStreetAddress+'<br>'+property_details.City+', '+property_details.StateOrProvince+' '+property_details.PostalCode;
                        if(property_details.ListingId) {
                            document.querySelector('#property_listing_id').innerHTML = property_details.ListingId;
                            document.querySelector('#property_status').innerHTML = property_details.MlsStatus;
                            document.querySelector('#property_list_office').innerHTML = property_details.ListOfficeName;
                            document.querySelector('#property_list_agent').innerHTML = property_details.ListAgentFirstName+' '+property_details.ListAgentLastName;
                            document.querySelector('#property_list_date').innerHTML = property_details.MLSListDate;
                            document.querySelector('#property_list_price').innerHTML = '$'+global_format_number(property_details.ListPrice);
                            document.querySelector('#property_type_display').innerHTML = property_details.PropertyType;
                        }
                        if(property_details.TaxRecordLink) {
                            scope.tax_records_link = true;
                            scope.property_found_tax_records = true;
                            document.querySelector('#property_tax_records_link').setAttribute('href', property_details.TaxRecordLink);
                        }
                        // let owners = property_details.Owner1;
                        // if(property_details.Owner2 != '') {
                        //     owners += ', '+property_details.Owner2;
                        // }
                        // document.querySelector('#property_owners').innerHTML = owners;

                        //  add to sessionStorage
                        sessionStorage.property_details = JSON.stringify(property_details);

                        scope.final_result = true;

                        scope.property_found_mls = false;
                        if(property_details.ListingId) {
                            scope.property_found_mls = true;
                            document.querySelector('#property_image').setAttribute('src', property_details.ListPictureURL);
                            scope.set_checklist_details();
                        }

                        // add if mls
                        if(mls_search) {
                            scope.address_header(property_details.StreetNumber, property_details.StreetName, property_details.UnitNumber, property_details.City, property_details.StateOrProvince, property_details.PostalCode);
                        }

                        setTimeout(function() {
                            window.scrollTo({ top: 5000, behavior: 'smooth' });
                        }, 100);

                    } else {

                        if(!mls_search) {

                            scope.search_type = 'manually';
                            scope.address_not_found = true;
                            document.getElementById('street_number').value = street_number;
                            document.getElementById('street_name').value = street_name;
                            document.getElementById('unit').value = unit_number;
                            document.getElementById('zip').value = zip;
                            document.getElementById('city').value = city;
                            document.getElementById('state').value = state;
                            let event = new Event('change');
                            document.getElementById('state').dispatchEvent(event);
                            setTimeout(function() {
                                document.getElementById('county').value = county;
                            }, 300);

                        }

                        sessionStorage.removeItem('property_details');

                    }

                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            get_location_details(container, member_id, zip, city, state, county = null) {

                let zip_code = zip.value;
                if(member_id) {
                    container = document.querySelector(container+'[data-id="'+member_id+'"]');
                } else {
                    container = document.querySelector(container);
                }
                city = container.querySelector(city);
                state = container.querySelector(state);
                county = container.querySelector(county) || null;

                if(zip_code.length == 5) {
                    axios.get('/transactions/get_location_details', {
                        params: {
                            zip: zip_code
                        },
                    })
                    .then(function (response) {
                        city.value = response.data.city;
                        state.value = response.data.state;
                        if(county) {
                            let event = new Event('change');
                            state.dispatchEvent(event);
                            setTimeout(function() {
                                county.value = response.data.county;
                            }, 200);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
            },
            save_manual_entry() {

                remove_form_errors();
                document.querySelector('#checklist_details_div').querySelectorAll('input, select').forEach(function(input) {
                    input.value = '';
                });

                let scope = this;

                let street_number = document.getElementById('street_number').value;
                let street_name = document.getElementById('street_name').value;
                let unit_number = document.getElementById('unit').value;
                let city = document.getElementById('city').value;
                let state = document.getElementById('state').value;
                let zip = document.getElementById('zip').value;
                let county = document.getElementById('county').value;

                //  add to sessionStorage
                let property_details = {
                    'FullStreetAddress': street_number+' '+street_name,
                    'Unit': unit,
                    'City': city,
                    'StateOrProvince': state,
                    'PostalCode': zip,
                    'County': county,
                };

                //validate
                let form = document.getElementById('manual_entry_form');
                let formData = new FormData(form);

                axios.post('/transactions/validate_form_manual_entry', formData)
                .then(function (response) {
                    sessionStorage.property_details = JSON.stringify(property_details);

                    // go to step 2
                    scope.active_step = 2;
                    scope.steps_complete = 1;

                    scope.address_header(street_number, street_name, unit_number, city, state, zip);

                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });



            },
            set_checklist_details() {

                let transaction_type = this.transaction_type;
                let property_details = JSON.parse(sessionStorage.property_details);
                //console.log(property_details);

                if(property_details.ListingId) {

                    let property_type = property_details.PropertyType;
                    let property_type_value = property_type.replace(/\sLease/, '');
                    property_type_value = property_type_value.replace(/\sSale/, '');
                    this.property_type = property_type_value;
                    let property_sub_type = property_details.SaleType || null;


                    this.for_sale = property_type.match(/lease/i) ? 'no' : 'yes';


                    if(property_sub_type) {

                        let end = property_sub_type.indexOf(',');
                        if(end < 0) {
                            end = property_sub_type.length;
                        }
                        property_sub_type = property_sub_type.substring(0, end);

                        if(property_sub_type.match(/(hud|reo)/i)) {
                            property_sub_type = 'REO/Bank/HUD Owned';
                        } else if(property_sub_type.match(/foreclosure/i)) {
                            property_sub_type = 'Foreclosure';
                        } else if(property_sub_type.match(/auction/i)) {
                            property_sub_type = 'Auction';
                        } else if(property_sub_type.match(/(short|third)/i)) {
                            property_sub_type = 'Short Sale';
                        } else if(property_sub_type.match(/standard/i)) {
                            property_sub_type = 'Standard';
                        } else {
                            property_sub_type = '';
                        }

                        // if no results check new construction
                        if(property_sub_type == '') {
                            if(property_details.NewConstructionYN == 'Y') {
                                property_sub_type = 'New Construction';
                            }
                        }

                    }


                    let hoa_condo = 'none';
                    let condo = property_details.CondoYN || null;
                    if(condo && condo == 'Y') {
                        hoa_condo = 'condo';
                    }
                    let hoa = property_details.AssociationYN ?? null;
                    if(hoa && hoa == 'Y') {
                        if(property_details.AssociationFee > 0) {
                            hoa_condo = 'hoa';
                        }
                    }

                    let year_built = property_details.YearBuilt ?? null;
                    let list_price = property_details.ListPrice ?? null;

                    let for_sale = this.for_sale == 'yes' ? 'sale' : 'rental';
                    document.getElementById('SaleRent').value = for_sale;
                    document.getElementById('PropertyType').value = property_type_value;
                    document.getElementById('PropertySubType').value = property_sub_type;
                    document.getElementById('YearBuilt').value = year_built;
                    document.getElementById('HoaCondoFees').value = hoa_condo;

                } else {

                    document.querySelector('#checklist_details_div').querySelectorAll('input, select').forEach(function(input) {
                        input.value = '';
                    });

                }


            },
            check_checklist_details() {

                let scope = this;

                remove_form_errors();

                //validate
                let form = document.getElementById('checklist_details_div');
                let formData = new FormData(form);
                formData.append('for_sale', this.for_sale);
                formData.append('transaction_type', this.transaction_type);
                formData.append('show_disclosures', this.show_disclosures == true ? 'yes' : 'no');

                axios.post('/transactions/validate_form_checklist_details', formData)
                .then(function (response) {

                    let checklist_details = {
                        'Agent_ID': document.getElementById('Agent_ID').value,
                        'SaleRent': document.getElementById('SaleRent').value,
                        'PropertyType': document.getElementById('PropertyType').value,
                        'PropertySubType': document.getElementById('PropertySubType').value,
                        'YearBuilt': document.getElementById('YearBuilt').value,
                        'HoaCondoFees': document.getElementById('HoaCondoFees').value
                    };

                    sessionStorage.setItem('checklist_details', JSON.stringify(checklist_details));
                    //console.log(sessionStorage.checklist_details);
                    // go to step 2
                    scope.active_step = 3;
                    scope.steps_complete = 2;
                })
                .catch(function (error) {
                    if(error) {
                        console.log(error);
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });
            },
            property_type_selected() {

                let prop_type = document.getElementById('PropertyType');
                let prop_sub_type = document.getElementById('PropertySubType');


                if(prop_type.value == 'Residential') {
                    this.show_disclosures = true;
                } else {
                    this.show_disclosures = false;
                    return false;
                }

                let need_disclosures = ['Standard', 'Short Sale', 'For Sale By Owner'];
                if(need_disclosures.includes(prop_sub_type.value)) {
                    this.show_disclosures = true;
                } else {
                    this.show_disclosures = false;
                }

                if(this.transaction_type == 'contract') {
                    if(this.for_sale == 'yes') {
                        document.getElementById('CloseDate').closest('label').querySelector('.label-text').innerText = 'Settlement Date';
                    } else {
                        document.getElementById('CloseDate').closest('label').querySelector('.label-text').innerText = 'Lease Date';
                        this.show_disclosures = false;
                    }
                }

            },
            clear_results_and_errors() {
                this.final_result = false;
                this.show_license_state_error = false;
                this.show_no_property_error = false;
                this.show_street_error = false;
                this.tax_records_link = false;
                this.address_not_found = false;
                sessionStorage.removeItem('property_details');
            },
            address_header(street_number, street_name, unit_number, city, state, zip) {

                let address = street_number+' '+street_name;
                if(unit_number != '') {
                    address += ' #'+unit_number;
                }
                address += ' '+city+', '+state+' '+zip;
                document.querySelectorAll('.address-header').forEach(function(header) {
                    header.innerHTML = address;
                });

                let referral_street = document.getElementById('ReferralClientStreet');
                let referral_city = document.getElementById('ReferralClientCity');
                let referral_state = document.getElementById('ReferralClientState');
                let referral_zip = document.getElementById('ReferralClientZip');
                if(referral_street) {
                    referral_street.setAttribute('data-default-value', street_number+' '+street_name);
                    if(unit_number != '') {
                        referral_street.setAttribute('data-default-value', street_number+' '+street_name+' #'+unit_number);
                    }
                    referral_city.setAttribute('data-default-value', city);
                    referral_state.setAttribute('data-default-value', state);
                    referral_zip.setAttribute('data-default-value', zip);
                }


            },
            get_contacts() {

                let cols = [
                    { data: 'import', orderable: false, searchable: false },
                    { data: 'contact_first' },
                    { data: 'contact_email' }
                ];
                let table = document.querySelector('#contacts_table');
                data_table('/transactions/get_contacts', cols, 10, $(table), [1, 'asc'], [0], [], false, true, true, true, true);
                table.classList.remove('hidden');
            },
            import_contact(ele) {
                let id = ele.getAttribute('data-id');
                let first = ele.getAttribute('data-first');
                let last = ele.getAttribute('data-last');
                let phone_cell = ele.getAttribute('data-phone_cell');
                let email = ele.getAttribute('data-email');
                let street = ele.getAttribute('data-street');
                let city = ele.getAttribute('data-city');
                let state = ele.getAttribute('data-state');
                let zip = ele.getAttribute('data-zip');
                let member_id = this.import_contact_member_id;

                let container = document.querySelector('[data-id="'+member_id+'"]');

                container.querySelector('.member-first').value = first;
                container.querySelector('.member-last').value = last;
                container.querySelector('.member-phone').value = phone_cell;
                container.querySelector('.member-email').value = email;
                container.querySelector('.member-street').value = street;
                container.querySelector('.member-city').value = city;
                container.querySelector('.member-state').value = state;
                container.querySelector('.member-zip').value = zip;

                this.show_add_contact_modal = false;

                global_format_phones();

            },
            add_member(member_type, seller_for_contract = false) {

                let container = document.querySelectorAll('.members-container')[0];
                if(seller_for_contract == true) {
                    container = document.querySelectorAll('.members-container')[1];
                }

                let member_html = document.querySelector('#member_template').innerHTML;
                if(seller_for_contract == true) {
                    member_html = document.querySelector('#member_seller_for_contract_template').innerHTML;
                }

                let last_member = container.querySelectorAll('.member-container:last-child')[0];
                let member_id = parseInt(last_member.getAttribute('data-id')) + 1;
                let member_count = parseInt(last_member.querySelector('.member-id').innerText) + 1;

                member_html = member_html.replace(/%%member_id%%/g, member_id);
                member_html = member_html.replace(/%%member_count%%/g, member_count);
                member_html = member_html.replace(/%%member_type%%/g, member_type);

                let div = document.createElement('div');
                div.innerHTML = member_html;
                container.appendChild(div);
                unwrap(div);
                document.querySelector('.new-member-div').classList.add('member-container');
                document.querySelector('.new-member-div').classList.remove('new-member-div');


            },
            remove_member(member_id, ele) {

                let container = ele.closest('.members-container');
                let member = container.querySelector('[data-id="'+member_id+'"]');
                member.classList.remove('opacity-100');
                member.classList.add('opacity-0');
                setTimeout(() => {
                    member.classList.add('h-0');
                    member.remove();
                    let c = 1;
                    container.querySelectorAll('.member-container').forEach(function(member_div) {
                        member_div.querySelector('.member-id').innerText = c;
                        c += 1;
                    });
                }, 700);



            },
            save_transaction(ele, transaction_type) {

                let type = ucwords(transaction_type);
                show_loading_button(ele, 'Saving '+type+'...');

                let sellers = [];
                let buyers = [];

                let members_container = document.querySelectorAll('.members-container');

                members_container.forEach(function(member_container) {

                    let type = member_container.getAttribute('data-type');

                    let members = member_container.querySelectorAll('.member-container');
                    if(members) {

                        let cont = true;

                        members.forEach(function(member) {

                            member.querySelectorAll('.required').forEach(function(required) {
                                if(required.value == '') {
                                    cont = false;
                                }
                            });

                            if(cont == true) {

                                let details = {
                                    'type': type
                                };

                                details.first_name = member.querySelector('.member-first').value;
                                details.last_name = member.querySelector('.member-last').value;
                                if(member.querySelector('.member-entity-name')) {
                                    details.entity_name = member.querySelector('.member-entity-name').value;
                                }
                                if(member.querySelector('.member-phone')) {
                                    details.phone = member.querySelector('.member-phone').value;
                                    details.email = member.querySelector('.member-email').value;
                                    details.street = member.querySelector('.member-street').value;
                                    details.city = member.querySelector('.member-city').value;
                                    details.state = member.querySelector('.member-state').value;
                                    details.zip = member.querySelector('.member-zip').value;
                                }

                                if(type == 'seller') {
                                    sellers.push(details);
                                } else if(type == 'buyer') {
                                    buyers.push(details);
                                }

                            }

                        });

                    }

                });


                if(sellers.length == 0 && transaction_type != 'referral') {
                    toastr.error('You must enter at least one Seller');
                    document.querySelector('.seller-header').scrollIntoView();
                    return false;
                }
                if(transaction_type == 'contract') {
                    if(buyers.length == 0) {
                        toastr.error('You must enter at least one Buyer');
                        document.querySelector('.buyer-header').scrollIntoView();
                        return false;
                    }
                }

                let form = document.querySelector('#create_form');
                let formData = new FormData(form);

                let required_details = {};
                formData.forEach(function(value, key){
                    required_details[key] = value;
                });

                formData.append('transaction_type', this.transaction_type);
                formData.append('checklist_details', sessionStorage.checklist_details);
                formData.append('property_details', sessionStorage.property_details);
                formData.append('required_details', JSON.stringify(required_details));
                formData.append('sellers', JSON.stringify(sellers));
                formData.append('buyers', JSON.stringify(buyers));

                axios.post('/transactions/save_transaction', formData)
                .then(function (response) {
                    console.log(response);
                })
                .catch(function (error) {
                    if(error) {
                        if(error.response.status == 422) {
                            let errors = error.response.data.errors;
                            show_form_errors(errors);
                        }
                    }
                });

            },
            agent_search(val) {
                let scope = this;
                axios.get('/transactions/agent_search', {
                    params: {
                        val: val
                    },
                })
                .then(function (response) {

                    let results_div = document.getElementById('agent_search_results');
                    results_div.innerHTML = '';

                    response.data.forEach(function(agent) {
                        let li = document.querySelector('#agent_search_result_template').innerHTML;
                        li = li.replace(/%%MemberFirstName%%/g, agent.MemberFirstName);
                        li = li.replace(/%%MemberLastName%%/g, agent.MemberLastName);
                        li = li.replace(/%%MemberType%%/g, agent.MemberType);
                        li = li.replace(/%%MemberMlsId%%/g, agent.MemberMlsId);
                        li = li.replace(/%%MemberEmail%%/g, agent.MemberEmail);
                        li = li.replace(/%%MemberPreferredPhone%%/g, agent.MemberPreferredPhone);
                        li = li.replace(/%%MemberMlsId%%/g, agent.MemberMlsId);
                        li = li.replace(/%%OfficePhone%%/g, agent.OfficePhone);
                        li = li.replace(/%%OfficeName%%/g, agent.OfficeName);
                        li = li.replace(/%%OfficeMlsId%%/g, agent.OfficeMlsId);
                        li = li.replace(/%%OfficeAddress1%%/g, agent.OfficeAddress1);
                        li = li.replace(/%%OfficeCity%%/g, agent.OfficeCity);
                        li = li.replace(/%%OfficeStateOrProvince%%/g, agent.OfficeStateOrProvince);
                        li = li.replace(/%%OfficePostalCode%%/g, agent.OfficePostalCode);

                        let div = document.createElement('div');
                        div.innerHTML = li;
                        results_div.appendChild(div);
                        unwrap(div);
                    });

                    scope.show_agent_search_results = true;

                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            select_agent(ele) {

                if(this.transaction_type == 'contract') {

                    document.getElementById('ListAgentFirstName').value = ele.getAttribute('data-MemberFirstName');
                    document.getElementById('ListAgentLastName').value = ele.getAttribute('data-MemberLastName');
                    document.getElementById('ListAgentOfficeName').value = ele.getAttribute('data-OfficeName');
                    document.getElementById('ListAgentMlsId').value = ele.getAttribute('data-MemberMlsId');
                    document.getElementById('ListAgentPreferredPhone').value = ele.getAttribute('data-MemberPreferredPhone');
                    document.getElementById('ListAgentEmail').value = ele.getAttribute('data-MemberEmail');
                    document.getElementById('ListAgentOfficeStreet').value = ele.getAttribute('data-OfficeAddress1');
                    document.getElementById('ListAgentOfficeCity').value = ele.getAttribute('data-OfficeCity');
                    document.getElementById('ListAgentOfficeState').value = ele.getAttribute('data-OfficeStateOrProvince');
                    document.getElementById('ListAgentOfficeZip').value = ele.getAttribute('data-OfficePostalCode');

                } else if(this.transaction_type == 'referral') {

                    document.getElementById('ReferralReceivingAgentFirstName').value = ele.getAttribute('data-MemberFirstName');
                    document.getElementById('ReferralReceivingAgentLastName').value = ele.getAttribute('data-MemberLastName');
                    document.getElementById('ReferralReceivingAgentOfficeName').value = ele.getAttribute('data-OfficeName');
                    document.getElementById('ReferralReceivingAgentOfficeStreet').value = ele.getAttribute('data-OfficeAddress1');
                    document.getElementById('ReferralReceivingAgentOfficeCity').value = ele.getAttribute('data-OfficeCity');
                    document.getElementById('ReferralReceivingAgentOfficeState').value = ele.getAttribute('data-OfficeStateOrProvince');
                    document.getElementById('ReferralReceivingAgentOfficeZip').value = ele.getAttribute('data-OfficePostalCode');

                }

                this.show_agent_search_results = false;
                document.querySelector('.agent-search-input').value = '';
            },
            set_referral_address() {

                let referral_street = document.getElementById('ReferralClientStreet');
                let referral_city = document.getElementById('ReferralClientCity');
                let referral_state = document.getElementById('ReferralClientState');
                let referral_zip = document.getElementById('ReferralClientZip');
                if(referral_street) {
                    referral_street.value = referral_street.getAttribute('data-default-value');
                    referral_city.value = referral_city.getAttribute('data-default-value');
                    referral_state.value = referral_state.getAttribute('data-default-value');
                    referral_zip.value = referral_zip.getAttribute('data-default-value');
                }

            },
            total_referral_commission() {

                let regex = new RegExp('[\$,]+', 'g');
                let total_commission = document.getElementById('ReferralCommissionAmount').value.replace(regex, '');
                let percent = parseInt(document.getElementById('ReferralReferralPercentage').value) / 100;
                let agent_commission = total_commission * percent;
                if(agent_commission > 0) {
                    document.getElementById('ReferralAgentCommission').value = agent_commission;
                    format_money_with_decimals(document.getElementById('ReferralAgentCommission'));
                }
            }

        }

    }

}


//sessionStorage.AgentID = document.getElementById('Agent_ID').value;
