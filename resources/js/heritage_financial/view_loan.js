if(document.URL.match(/view_loan/)) {


    window.loan = function() {

        return {

            active_tab: '1',
            show_other_title: false,
            init() {

                let scope = this;
                scope.set_commission_check_amount();
                scope.set_deductions_amount();
                scope.total_commission();
                scope.trigger_total();
                scope.focus_money();

            },
            trigger_total() {
                let scope = this;
                document.querySelectorAll('.commission-input').forEach(function(input) {
                    input.addEventListener('keyup', function(e) {
                        scope.total_commission();
                    });
                });
            },
            total_commission() {

                this.set_deductions_amount();

                let commission_check_amount = parseFloat(document.querySelector('#commission_check_amount').value.replace(/[,\$]/g, ''));
                let net_commission_amount_ele = document.querySelector('#net_commission_amount');
                let deductions_total = 0;
                document.querySelectorAll('[name="amount[]"]').forEach(function(amount) {
                    let value = parseFloat(amount.value.replace(/[,\$]/g, ''));
                    if(value != '') {
                        deductions_total += parseFloat(value);
                    }
                });
                let net_commission_amount = commission_check_amount - deductions_total;

                net_commission_amount_ele.innerText = global_format_number_with_decimals(net_commission_amount.toString());

            },
            save_details(ele) {

                show_loading_button(ele, 'Saving ... ');
                remove_form_errors();

                let form = document.getElementById('details_form');
                let formData = new FormData(form);

                let orig_uuid = document.getElementById('uuid');

                axios.post('/heritage_financial/save_details', formData)
                .then(function (response) {
                    if(orig_uuid != '') {
                        ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Details';
                        toastr.success('Loan Details successfully saved');
                    } else {
                        let uuid = response.data.uuid;
                        window.location = document.URL+'/'+uuid;
                    }
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

                let form = document.getElementById('commission_form');
                let formData = new FormData(form);

                let orig_uuid = document.getElementById('uuid');

                axios.post('/heritage_financial/save_commission', formData)
                .then(function (response) {
                    if(orig_uuid != '') {
                        ele.innerHTML = '<i class="fal fa-check mr-3"></i> Save Commission';
                        toastr.success('Loan Commission successfully saved');
                    } else {
                        let uuid = response.data.uuid;
                        window.location = document.URL+'/'+uuid;
                    }
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
            set_commission_check_amount() {
                document.querySelector('#commission_check_amount_ele').innerText = global_format_number_with_decimals(document.querySelector('#commission_check_amount').value);
            },
            set_deductions_amount() {
                let show_total = document.querySelector('#deductions_amount');
                let deductions_total = 0;
                document.querySelectorAll('[name="amount[]"]').forEach(function(amount) {
                    let value = amount.value.replace(/[,\$]/g, '');
                    amount = parseFloat(value);
                    deductions_total += amount;
                });
                show_total.innerText = global_format_number_with_decimals(deductions_total.toString());

            },
            add_deduction() {
                let deduction = document.getElementById('deduction_template').innerHTML;
                document.querySelector('.deductions').insertAdjacentHTML('beforeend', deduction);
                this.focus_money();
                global_format_money();
                numbers_only();
                this.trigger_total();
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
