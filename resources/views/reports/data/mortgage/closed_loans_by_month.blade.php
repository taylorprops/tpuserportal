<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            * {
                font-family: Arial, Helvetica, sans-serif;
            }
            .page-break {
                page-break-inside: avoid;
            }
            .section {
                border: 1px solid rgb(180, 180, 180);
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 15px;
            }
            .report-name {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 20px;
                color: rgb(70, 70, 70);
            }
            .section-header {
                font-size: 14px;
                color: rgb(90, 90, 90);
                font-weight: bold;
                margin-bottom: 10px;
            }
            .table-wrapper {
                border: 2px solid rgb(180, 180, 180);
                border-radius: 5px;
            }
            table.loans-table {

            }
            table.loans-table th {
                border-bottom: 2px solid rgb(200, 200, 200);
                padding: 6px;
                font-size: 12px;
                text-align: left;
                color: rgb(80, 80, 80);
                background:rgb(230, 230, 230)
            }
            table.loans-table td {
                border-bottom: 1px solid rgb(210, 210, 210);
                padding: 6px;
                font-size: 11px;
                color: rgb(90, 90, 90);
                white-space: nowrap;
            }

        </style>
    </head>

    <body>

        <div class="report-name">
            {{ $report_name }}
        </div>

        @foreach($years as $year)

        <div class="section page-break">

            <div class="section-header">
                {{ $year }}
            </div>

            <div class="table-wrapper">

                <table class="loans-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            @foreach($table_headers as $table_header)
                                <th style="">
                                    {{ $table_header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($months as $month)

                            @php
                            $total_loans = 0;
                            $total_loan_amount = 0;
                            $checks_in = 0;
                            $total_loan_officer_1_commission_amount = 0;
                            $total_loan_officer_2_commission_amount = 0;
                            $total_company_commission = 0;
                            $average_loan_amount = 0;
                            $total_manager_bonus = 0;

                            foreach($loans -> where('year', $year) -> where('month', $month) as $loan) {

                                $total_loans += 1;
                                $total_loan_amount += $loan -> loan_amount;
                                $total_loan_officer_1_commission_amount += $loan -> loan_officer_1_commission_amount;
                                $total_loan_officer_2_commission_amount += $loan -> loan_officer_2_commission_amount;
                                $total_company_commission += $loan -> company_commission;
                                $average_loan_amount += $loan -> loan_amount;
                                $total_manager_bonus += $loan -> manager_bonus;

                                $money_in = 0;
                                $deductions = 0;
                                foreach($loan -> checks_in as $check_in) {
                                    $money_in += $check_in -> amount;
                                }
                                foreach($loan -> deductions as $deduction) {
                                    $deductions += $deduction -> amount;
                                }
                                $money_in = $money_in - $deductions;
                                $checks_in += $money_in;

                            }
                            @endphp

                            @if($total_loans > 0)
                                <tr>
                                    <td>{{ date('F', strtotime('2000-'.$month.'-01')) }}</td>
                                    <td>{{ $total_loans }}</td>
                                    <td>${{ number_format($total_loan_amount) }}</td>
                                    <td>${{ number_format($checks_in) }}</td>
                                    <td>${{ number_format($total_loan_officer_1_commission_amount) }}/${{ number_format($total_loan_officer_2_commission_amount) }}</td>
                                    <td>${{ number_format($total_manager_bonus) }}</td>
                                    <td>${{ number_format($total_company_commission) }}</td>
                                    <td>${{ number_format($average_loan_amount / $total_loans) }}</td>
                                </tr>
                            @endif

                        @endforeach

                    </tbody>
                </table>

            </div>

        </div>

        @endforeach


    </body>

</html>
