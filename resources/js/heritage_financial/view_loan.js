if(document.URL.match(/view_loan/)) {


    window.loan = function(loan_officer_1_commission_type, loan_officer_2_commission_type, loan_officer_2_commission_sub_type, loan_amount, points_charged, manager_bonus, loan_officer_1_loan_amount_percent, loan_officer_2_loan_amount_percent, sub_type) {

        return {

            active_tab: '1',
            show_other_title: false,
            show_delete_check_in: false,
            loan_officer_1_commission_amount: '',
            loan_officer_1_commission_type: loan_officer_1_commission_type,
            loan_officer_2_commission_type: loan_officer_2_commission_type || null,
            loan_officer_2_commission_sub_type: loan_officer_2_commission_sub_type || null,
            loan_amount: loan_amount,
            points_charged: points_charged,
            manager_bonus: manager_bonus,
            loan_officer_1_loan_amount_percent: loan_officer_1_loan_amount_percent,
            loan_officer_2_loan_amount_percent: loan_officer_2_loan_amount_percent || null,
            sub_type: sub_type,
            init() {

                this.total_commission();
                this.trigger_total();
                this.focus_money();

                this.run_show_delete_check_in();

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

                let net_commission_amount_ele = document.querySelector('#net_commission_amount');
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

                        if(scope.sub_type == 'loan_officer_commission' && index === '2') {
                            loan_officer_commission = (loan_officer_commission_percent / 100) * scope.loan_officer_1_commission_amount;
                        }

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

                            alert_icon = '<i class="fal fa-check fa-2x mr-3"></i>';
                            alert_text = 'Commission is more than 2.5 basis points.';
                            details = loan_officer_loan_amount_percent+'% of '+global_format_number_with_decimals(loan_amount.toString())+' = <span class="text-lg font-bold">'+global_format_number_with_decimals(loan_officer_commission.toString())+'</span>';

                        }


                        document.querySelector('#loan_officer_'+index+'_loan_amount_alert_icon').innerHTML = alert_icon;
                        document.querySelector('#loan_officer_'+index+'_loan_amount_alert_text').innerHTML = alert_text;
                        document.querySelector('#loan_officer_'+index+'_loan_amount_details').innerHTML = details;
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

                document.querySelector('.deductions-out-div').innerHTML = '';
                document.querySelectorAll('.deduction').forEach(function(deduction) {

                    let amount = deduction.querySelector('[name="amount[]"]');
                    let description = deduction.querySelector('[name="description[]"]');
                    let paid_to = deduction.querySelector('[name="paid_to[]"]');
                    let paid_to_value = paid_to.options[paid_to.selectedIndex].value;
                    let paid_to_other = deduction.querySelector('[name="paid_to_other[]"]');

                    if(paid_to_value != 'Company') {

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

                    }

                });


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
            save_commission(ele) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.querySelector('#commission_form');
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
            set_checks_in_amount() {

                let show_total = document.querySelector('#checks_in_amount_ele');
                let checks_in_total = 0;
                document.querySelectorAll('[name="check_in_amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = amount.value.replace(/[,\$]/g, '');
                        amount = parseFloat(value);
                        checks_in_total += amount;
                    }
                });
                show_total.innerText = global_format_number_with_decimals(checks_in_total.toString());

            },
            set_deductions_amount() {
                let show_total = document.querySelector('#deductions_amount');
                let deductions_total = 0;
                document.querySelectorAll('[name="amount[]"]').forEach(function(amount) {
                    if(amount.value != '') {
                        let value = amount.value.replace(/[,\$]/g, '');
                        amount = parseFloat(value);
                        deductions_total += amount;
                    }
                });
                show_total.innerText = global_format_number_with_decimals(deductions_total.toString());

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
            }

        }

    }

}
