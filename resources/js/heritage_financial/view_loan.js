if(document.URL.match(/view_loan/)) {


    window.loan = function(uuid, active_tab, loan_officer_1_commission_type, loan_officer_2_commission_type, loan_amount, points_charged, manager_bonus, loan_officer_1_loan_amount_percent, loan_officer_2_loan_amount_percent) {

        return {

            active_tab: active_tab,
            show_other_title: false,
            show_delete_check_in: false,
            loan_officer_1_commission_amount: '',
            loan_officer_1_commission_type: loan_officer_1_commission_type,
            loan_officer_2_commission_type: loan_officer_2_commission_type || null,
            loan_amount: loan_amount,
            points_charged: points_charged,
            manager_bonus: manager_bonus,
            loan_officer_1_loan_amount_percent: loan_officer_1_loan_amount_percent,
            loan_officer_2_loan_amount_percent: loan_officer_2_loan_amount_percent || null,
            show_alert: false,
            show_deleted_docs_div: false,
            show_deleted: false,
            uuid: uuid,
            show_add_notes: false,
            init() {

                this.total_commission();
                this.trigger_total();
                this.focus_money();

                this.run_show_delete_check_in();

                this.docs();
                this.get_docs();

                this.get_notes();

            },
            trigger_total() {
                let scope = this;
                ['keyup', 'blur'].forEach(function(event) {
                    document.querySelectorAll('.commission-input').forEach(function(input) {
                        input.addEventListener(event, function(e) {
                            scope.total_commission();
                        });
                    });
                });
            },
            total_commission() {

                let scope = this;

                let checks_in_total = 0;
                document.querySelectorAll('[name="check_in_amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = parseFloat(amount.value.replace(/[,\$]/g, ''));
                        checks_in_total += parseFloat(value);
                    }
                });

                let net_commission_amount_ele = document.querySelector('.net-commission-amount');
                let deductions_total = 0;
                document.querySelectorAll('[name="amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = parseFloat(amount.value.replace(/[,\$]/g, ''));
                        deductions_total += parseFloat(value);
                    }
                });
                let net_commission_amount = checks_in_total - deductions_total;

                net_commission_amount_ele.innerText = global_format_number_with_decimals(net_commission_amount.toString());
                document.querySelectorAll('.net-commission-amount').forEach(function(ele) {
                    ele.innerText = global_format_number_with_decimals(net_commission_amount.toString());
                });


                let los = document.querySelector('#loan_officer_2_commission') ? ['1', '2'] : ['1'];

                los.forEach(function(index) {

                    let commission_type = index === '1' ? scope.loan_officer_1_commission_type : scope.loan_officer_2_commission_type;
                    let loan_officer_commission = '';
                    let loan_officer_loan_amount_percent = index === '1' ? scope.loan_officer_1_loan_amount_percent : scope.loan_officer_2_loan_amount_percent;

                    if(commission_type == 'commission') {

                        let loan_officer_commission_percent = document.querySelector('[name="loan_officer_'+index+'_commission_percent"]').value;
                        loan_officer_commission = (loan_officer_commission_percent / 100) * net_commission_amount;

                    } else if(commission_type == 'loan_amount') {

                        let alert_icon, alert_text, details;

                        let points = parseFloat(scope.points_charged);

                        if(points < 2.5) {

                            let commission_deduction = (2.5 - points).toFixed(3) / 2;
                            let commission_deduction_adjusted = (parseFloat(loan_officer_loan_amount_percent) - commission_deduction).toFixed(3);

                            document.querySelector('[name="loan_officer_'+index+'_loan_amount_percent"]').value = commission_deduction_adjusted;

                            loan_officer_commission = (commission_deduction_adjusted * loan_amount) / 100;

                            alert_icon = '<i class="fad fa-exclamation-circle fa-2x mr-3"></i>';
                            alert_text = 'Commission is <span class="text-red-800">'+points.toFixed(2)+' basis points</span> - less than 2.5.';
                            details = '2.5 - '+points.toFixed(3)+' = '+(2.5 - points).toFixed(3)+' / 2 = '+commission_deduction+'<br> \
                                '+loan_officer_loan_amount_percent+' - '+commission_deduction+' = '+commission_deduction_adjusted+'<br> \
                                <span>'+commission_deduction_adjusted+'% of <span ml-3">'+global_format_number_with_decimals(loan_amount.toString())+'</span> = <span class="text-lg font-bold">'+global_format_number_with_decimals(loan_officer_commission.toString())+'</span></span>';

                        } else {

                            document.querySelector('[name="loan_officer_'+index+'_loan_amount_percent"]').value = loan_officer_loan_amount_percent;

                            loan_officer_commission = (parseFloat(loan_officer_loan_amount_percent) * loan_amount) / 100;

                            alert_icon = null;
                            alert_text = null;
                            details = loan_officer_loan_amount_percent+'% <span class="ml-4">of</span> <span class="ml-4">'+global_format_number_with_decimals(loan_amount.toString())+'</span> <span class="ml-4">=</span> <span class="text-lg font-bold ml-4">'+global_format_number_with_decimals(loan_officer_commission.toString())+'</span>';

                        }


                        if(alert_icon) {
                            document.querySelector('#loan_officer_'+index+'_loan_amount_alert_icon').innerHTML = alert_icon;
                            document.querySelector('#loan_officer_'+index+'_loan_amount_alert_text').innerHTML = alert_text;
                            scope.show_alert = true;
                        } else {
                            scope.show_alert = false;
                        }

                        document.querySelectorAll('.loan-officer-'+index+'-loan-amount-details').forEach(function(ele) {
                            ele.innerHTML = details;
                        });

                    }

                    let loan_officer_deductions_total = 0;
                    let deduction_container = document.querySelector('.loan-officer-'+index+'-deductions');
                    deduction_container.querySelectorAll('[name="loan_officer_deduction_amount[]"]').forEach(function(amount) {
                        if(amount.value != '') {
                            let value = parseFloat(amount.value.replace(/[,\$]/g, ''));
                            loan_officer_deductions_total += parseFloat(value);
                        }
                    });

                    document.querySelectorAll('.loan-officer-'+index+'-commission-amount').forEach(function(span) {
                        span.innerHTML = global_format_number_with_decimals(loan_officer_commission.toString());
                    });

                    if(index === '1') {
                        scope.loan_officer_1_commission_amount = loan_officer_commission;
                    }

                    loan_officer_commission = loan_officer_commission - loan_officer_deductions_total;

                    document.querySelectorAll('.loan-officer-'+index+'-commission-amount-ele').forEach(function(ele) {
                        ele.innerText = global_format_number_with_decimals(loan_officer_commission.toString());
                    });

                    document.querySelector('#loan_officer_'+index+'_commission_amount').value = loan_officer_commission;


                    let manager_bonus_amount = (parseFloat(scope.manager_bonus) / 100)  * net_commission_amount;

                    document.querySelector('#manager_bonus').value = manager_bonus_amount;
                    document.querySelectorAll('.manager-commission-amount-ele').forEach(function(ele) {
                        ele.innerHTML = global_format_number_with_decimals(manager_bonus_amount.toString());
                    });


                    let commissions_paid_total = 0;
                    document.querySelectorAll('.commission-paid-out').forEach(function(amount) {
                        if(amount.value != '') {
                            let value = amount.value.replace(/[,\$]/g, '');
                            amount = parseFloat(value);
                            commissions_paid_total += amount;
                        }
                    });

                    let company_commission = net_commission_amount - (commissions_paid_total);

                    document.querySelector('#company_commission').value = company_commission;
                    document.querySelector('#company_commission_amount').innerHTML = global_format_number_with_decimals(company_commission.toString());

                });

                if(document.querySelectorAll('.deduction').length > 0) {

                    document.querySelector('.deductions-out-div').innerHTML = '';
                    document.querySelector('.deductions-out-div-print').innerHTML = '';

                    let deduction_html_print = ' \
                    <table style="font-family:Arial, Helvetica, sans-serif; margin-top: 20px"> \
                        <tr> \
                            <th align="left">Other</th> \
                        </tr>';

                    document.querySelectorAll('.deduction').forEach(function(deduction) {

                        let amount = deduction.querySelector('[name="amount[]"]');
                        let description = deduction.querySelector('[name="description[]"]');
                        let paid_to = deduction.querySelector('[name="paid_to[]"]');
                        let paid_to_value = paid_to.options[paid_to.selectedIndex].value;
                        let paid_to_other = deduction.querySelector('[name="paid_to_other[]"]');

                        if(paid_to_value != 'Company' && paid_to_other.value != 'Company') {

                            let paid_to_name = paid_to.options[paid_to.selectedIndex].text;
                            if(paid_to_value == 'Other') {
                                paid_to_name = paid_to_other.value;
                            }
                            let deduction_html = ' \
                            <div class="col-span-2 border-b border-white py-2"> \
                                <div class="grid grid-cols-3"> \
                                    <div class="col-span-2"> \
                                        '+paid_to_name+' \
                                    </div> \
                                    <div> \
                                        '+global_format_number_with_decimals(amount.value.toString())+' \
                                    </div> \
                                </div> \
                                <div class="text-sm text-gray-500">'+description.value+'</div> \
                            </div> \
                            ';
                            document.querySelector('.deductions-out-div').insertAdjacentHTML('beforeend', deduction_html);

                            deduction_html_print += ' \
                            <tr> \
                                <td style="padding-right: 10px">'+paid_to_name+'</td> \
                                <td style="padding-right: 10px">'+global_format_number_with_decimals(amount.value.toString())+'</td> \
                                <td>'+description.value+'</td> \
                            </tr>';

                        }

                    });

                    deduction_html_print += '</table>';

                    document.querySelector('.deductions-out-div-print').insertAdjacentHTML('beforeend', deduction_html_print);

                }

                this.set_checks_in_amount();
                this.set_deductions_amount();
                this.set_commissions_paid_amount();

            },
            save_details(ele) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.querySelector('#details_form');
                let formData = new FormData(form);

                let orig_uuid = document.querySelector('[name="uuid"]').value;

                axios.post('/heritage_financial/save_details', formData)
                .then(function (response) {

                    ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Details';
                    toastr.success('Loan Details successfully saved');

                    setTimeout(function() {
                        if(orig_uuid != '') {
                            window.location = document.URL;
                        } else {
                            window.location = document.URL+'/'+response.data.uuid;
                        }
                    }, 500);

                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Details';
                            }
                        }
                    }
                });

            },
            save_commission(ele, user) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = '';
                if(user == 'lo') {
                    form = document.querySelector('#commission_form_lo');
                } else {
                    form = document.querySelector('#commission_form');
                }
                let formData = new FormData(form);

                axios.post('/heritage_financial/save_commission', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Commission';
                    toastr.success('Loan Commission successfully saved');
                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Commission';
                            }
                        }
                    }
                });

            },
            save_time_line(ele) {
                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.getElementById('time_line_form');
                let formData = new FormData(form);

                axios.post('/heritage_financial/save_time_line', formData)
                .then(function (response) {
                    ele.innerHTML = 'Save Time Line <i class="fal fa-check ml-2"></i>';
                    toastr.success('Time Line Successfully Saved');
                })
                .catch(function (error) {

                });
            },
            set_checks_in_amount() {

                let show_total = document.querySelectorAll('.checks-in-amount');
                let checks_in_total = 0;
                document.querySelectorAll('[name="check_in_amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = amount.value.replace(/[,\$]/g, '');
                        amount = parseFloat(value);
                        checks_in_total += amount;
                    }
                });
                show_total.forEach(function(ele) {
                    ele.innerText = global_format_number_with_decimals(checks_in_total.toString());
                });

            },
            set_deductions_amount() {

                let deductions_eles = document.querySelectorAll('.deductions-total');
                let deductions_total = 0;
                let deductions_checks_in = document.querySelector('.deductions-checks-in');

                document.querySelectorAll('[name="amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = amount.value.replace(/[,\$]/g, '');
                        amount = parseFloat(value);
                        deductions_total += amount;
                    }
                });
                deductions_eles.forEach(function(ele) {
                    ele.innerText = global_format_number_with_decimals(deductions_total.toString());
                });

                document.querySelectorAll('.deduction').forEach(function(deduction) {
                    let amount = deduction.querySelector('[name="amount[]"]').value;
                    let description = deduction.querySelector('[name="description[]"]').value;
                    //let paid_to = deduction.querySelector('[name="paid_to_other[]"]').value;
                    if(amount != '') {
                        let deduction_html = ' \
                        <div class="grid grid-cols-2 py-2"> \
                        <div class="">'+description+'</div> \
                        <div class="text-right">$'+amount+'</div> \
                        </div> \
                        ';
                        deductions_checks_in.innerHTML += deduction_html;
                    }
                });

            },
            set_commissions_paid_amount() {
                let show_total = document.querySelector('#commissions_paid_amount');
                let commissions_paid_total = 0;
                document.querySelectorAll('.commission-paid-out').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = amount.value.replace(/[,\$]/g, '');
                        amount = parseFloat(value);
                        commissions_paid_total += amount;
                    }
                });
                show_total.innerText = global_format_number_with_decimals(commissions_paid_total.toString());

            },
            add_check_in() {
                let check_in = document.querySelector('#check_in_template').innerHTML;
                document.querySelector('.checks-in').insertAdjacentHTML('beforeend', check_in);
                document.querySelector('.checks-in').lastElementChild.getElementsByTagName('input')[0].select();
                this.focus_money();
                global_format_money();
                numbers_only();
                this.trigger_total();
                this.run_show_delete_check_in();
            },
            add_deduction() {
                let deduction = document.querySelector('#deduction_template').innerHTML;
                document.querySelector('.deductions').insertAdjacentHTML('beforeend', deduction);
                document.querySelector('.deductions').lastElementChild.getElementsByTagName('input')[0].select();
                this.focus_money();
                global_format_money();
                numbers_only();
                this.trigger_total();
            },
            add_loan_officer_deduction(index) {
                let deduction = document.querySelector('#loan_officer_deduction_template').innerHTML;
                deduction = deduction.replace(/%%index%%/g, index);
                document.querySelector('.loan-officer-'+index+'-deductions').insertAdjacentHTML('beforeend', deduction);
                document.querySelector('.loan-officer-'+index+'-deductions').lastElementChild.getElementsByTagName('input')[0].select();
                this.focus_money();
                global_format_money();
                numbers_only();
                this.trigger_total();
            },
            run_show_delete_check_in() {
                if(document.querySelectorAll('.check-in').length > 1) {
                    this.show_delete_check_in = true;
                } else {
                    this.show_delete_check_in = false;
                }
            },

            focus_money() {
                document.querySelectorAll('.money-decimal').forEach(function(input) {
                    input.addEventListener('focus', function() {
                        this.select();
                    });
                });
            },

            require_title(status) {
                this.$refs.title_company.classList.remove('required');
                this.$refs.title_company_select.classList.remove('required');
                if(status == 'Closed') {
                    this.$refs.title_company.classList.add('required');
                    this.$refs.title_company_select.classList.add('required');
                }
            },

            require_close_date(status) {
                this.$refs.settlement_date.classList.remove('required');
                if(status == 'Closed' || status == 'Cancelled') {
                    this.$refs.settlement_date.classList.add('required');
                }
            },

            docs() {

                let scope = this;
                let loan_docs = document.getElementById('loan_docs');
                scope.loan_docs_pond = FilePond.create(loan_docs);

                scope.loan_docs_pond.setOptions({
                    allowImagePreview: false,
                    multiple: true,
                    server: {
                        process: {
                            url: '/heritage_financial/loans/docs_upload',
                            onerror: (response) => response.data,
                            ondata: (formData) => {
                                formData.append('uuid', scope.uuid);
                                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));
                                return formData;
                            }
                        }
                    },
                    labelIdle: 'Drag & Drop here or <span class="filepond--label-action"> Browse </span>',
                    onprocessfiles: () => {
                        scope.loan_docs_pond.removeFiles();
                        scope.get_docs();
                    }
                });
            },
            get_docs() {

                let scope = this;
                let formData = new FormData();
                formData.append('uuid', uuid);
                formData.append('_token', document.querySelector('[name="csrf-token"]').getAttribute('content'));

                axios.post('/heritage_financial/loans/get_docs', formData, axios_options)
                .then(function (response) {

                    document.querySelector('.docs-div').innerHTML = '';
                    document.querySelector('.deleted-docs-div').innerHTML = '';
                    scope.show_deleted_docs_div = false;
                    scope.show_deleted = false;
                    document.querySelector('#check_all').checked = false;
                    document.querySelector('#check_all_deleted').checked = false;

                    if(response.data.docs.length > 0) {

                        response.data.docs.forEach(function(doc) {

                            if(doc.trashed === true) {
                                scope.show_deleted_docs_div = true;
                            }

                            let div = doc.trashed === false ? 'docs-div': 'deleted-docs-div';
                            let input_class = doc.trashed === false ? 'doc-input': 'deleted-doc-input';

                            let html = '<div id="doc_'+doc.id+'">'+document.querySelector('#doc_template').innerHTML+'</div>';
                            html = html.replace(/%%doc_id%%/g, doc.id);
                            html = html.replace(/%%file_name%%/g, doc.file_name);
                            html = html.replace(/%%url%%/g, doc.file_location_url);
                            html = html.replace(/%%file_size%%/g, doc.file_size);
                            html = html.replace(/%%created%%/g, doc.created);
                            html = html.replace(/%%input_class%%/g, input_class);


                            document.querySelector('.'+div).insertAdjacentHTML('beforeend', html);

                            let button = '.restore-button';
                            if(doc.trashed === true) {
                                button = '.delete-button';
                            }

                            setTimeout(function() {
                                document.querySelector('#doc_'+doc.id).querySelector(button).remove();
                            }, 100);

                        });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            delete_docs(id = null) {

                let confirm_text = id ? 'this document' : 'these documents';
                let s = id ? '' : 's';

                let ids = [];
                if(id) {
                    ids.push(id);
                } else {
                    document.querySelectorAll('.doc-input:checked').forEach(function(input) {
                        ids.push(input.value);
                    });
                }

                if(confirm('Are you sure you want to delete '+confirm_text+'?')) {
                    let scope = this;
                    let formData = new FormData();
                    formData.append('ids', ids);
                    axios.post('/heritage_financial/loans/delete_docs', formData)
                    .then(function (response) {
                        scope.get_docs();
                        toastr.success('Document'+s+' deleted successfully.');
                    })
                    .catch(function (error) {
                    });
                }
            },

            restore_docs(id = null) {

                let s = id ? '' : 's';

                let ids = [];
                if(id) {
                    ids.push(id);
                } else {
                    document.querySelectorAll('.deleted-doc-input:checked').forEach(function(input) {
                        ids.push(input.value);
                    });
                }

                let scope = this;
                let formData = new FormData();
                formData.append('ids', ids);
                axios.post('/heritage_financial/loans/restore_docs', formData)
                .then(function (response) {
                    scope.get_docs();
                    toastr.success('Document'+s+' restored successfully.');
                })
                .catch(function (error) {
                });
            },

            check_all(deleted) {

                let check_all = !deleted ? document.querySelector('#check_all').checked : document.querySelector('#check_all_deleted').checked;
                let inputs = !deleted ? '.doc-input' : '.deleted-doc-input';
                document.querySelectorAll(inputs).forEach(function (input) {
                    input.checked = check_all;
                });

                this.show_bulk_options(deleted);
            },

            show_bulk_options(deleted) {

                let checked = false;
                let inputs = !deleted ? '.doc-input' : '.deleted-doc-input';
                let bulk_options = !deleted ? this.$refs.bulk_options : this.$refs.bulk_options_deleted;

                document.querySelectorAll(inputs).forEach(function (input) {
                    if(input.checked === true) {
                        checked = true;
                    }
                });


                bulk_options.querySelectorAll('button').forEach(function(button) {
                    if(checked === true) {
                        button.removeAttribute('disabled');
                    } else {
                        button.setAttribute('disabled', 'disabled');
                    }
                });
            },

            get_notes() {
                let scope = this;
                axios.get('/heritage_financial/get_notes', {
                    params: {
                        uuid: scope.uuid
                    },
                })
                .then(function (response) {
                    scope.$refs.notes_div.innerHTML = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            add_notes(ele) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let scope = this;
                let form = document.querySelector('#add_notes_form');
                let formData = new FormData(form);
                formData.append('uuid', uuid);

                axios.post('/heritage_financial/add_notes', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save Note';
                    scope.get_notes();
                    scope.show_add_notes = false;
                    toastr.success('Note Saved');
                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-check mr-2"></i> Save Note';
                            }
                        }
                    }
                });
            },

            delete_note(ele, id) {
                let scope = this;
                show_loading_button(ele, 'Deleting ... ');
                remove_form_errors();

                let formData = new FormData();
                formData.append('id', id);

                axios.post('/heritage_financial/delete_note', formData)
                .then(function (response) {
                    ele.innerHTML = '<i class="fal fa-times mr-2"></i> Delete';
                    scope.get_notes();
                    toastr.success('Note Deleted');
                })
                .catch(function (error) {
                    if (error) {
                        if (error.response) {
                            if (error.response.status == 422) {
                                let errors = error.response.data.errors;
                                show_form_errors(errors);
                                ele.innerHTML = '<i class="fal fa-times mr-2"></i> Delete';
                            }
                        }
                    }
                });
            },

            print_checks_out() {
                let print_page = window.open('');
                print_page.document.write(document.querySelector('.printable-checks-out').innerHTML);
                print_page.stop();
                print_page.print();
                print_page.close();
            }

        }

    }

}
