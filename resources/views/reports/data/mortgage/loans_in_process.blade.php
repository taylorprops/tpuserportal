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
            .loan-officer-div {
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
            .loan-officer-name {
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
            .loans-summary {
                margin-top: 10px;
                font-weight: bold;
                font-size: 12px;
                color: rgb(80, 80, 80);
            }
        </style>
    </head>

    <body>

        <div class="report-name">
            {{ $report_name }}
        </div>

        @foreach($loan_officers as $loan_officer)

            <div class="loan-officer-div page-break">

                <div class="loan-officer-name">
                    {{ $loan_officer -> fullname }}
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

                            @php
                                $loans = $loan_officer -> loans;
                                $loans_count = count($loans);
                                $loan_amounts_total = 0;
                                $commission_total = 0;
                                @endphp

                            @foreach($loans as $loan)

                                @php
                                $borrower = $loan -> borrower_fullname;
                                if($loan -> co_borrower_fullname) {
                                    $borrower .= '<br>'.$loan -> co_borrower_fullname;
                                }

                                $agent_seller = null;
                                $agent_buyer = null;

                                $agent_name_seller = $loan -> agent_name_seller;
                                if($loan -> agent_company_seller != '') {
                                    $agent_seller = $agent_name_seller .= ' - '.$loan -> agent_company_seller;
                                }

                                $agent_name_buyer = $loan -> agent_name_buyer;
                                if($loan -> agent_company_buyer != '') {
                                    $agent_buyer = $agent_name_buyer .= ' - '.$loan -> agent_company_buyer;
                                }

                                $agents = $agent_seller;
                                if($agent_buyer) {
                                    $agents .= '<br>'.$agent_buyer;
                                }

                                $loan_amounts_total += $loan -> loan_amount;
                                $commission_total += $loan -> company_commission;
                                @endphp

                                <tr>
                                    <td>{!! $borrower !!}</td>
                                    <td>{!! $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip !!}</td>
                                    <td>{{ $loan -> purpose == 'refi' ? 'Refi' : 'Purchase' }}</td>
                                    <td>{{ $loan -> processor -> fullname }}</td>
                                    <td>{!! $agents !!}</td>
                                    <td>{{ $loan -> time_line_sent_to_processing }}</td>
                                    <td>{{ $loan -> time_line_conditions_received ?? null }}</td>
                                    <td>{{ $loan -> lock_expiration }}</td>
                                    <td>{{ $loan -> settlement_date }}</td>
                                    <td>${{ number_format($loan -> loan_amount) }}</td>
                                    <td>${{ number_format($loan -> company_commission) }}</td>
                                    <td>{{ $loan -> title_company }}</td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="loans-summary">
                    <table class="loans-summary-table">
                        <tr>
                            <td align="right" style="padding-right: 8px">Total Loans</td>
                            <td>{{ $loans_count }}</td>
                        </tr>
                        <tr>
                            <td align="right" style="padding-right: 8px">Total Loans Amount</td>
                            <td>${{ number_format($loan_amounts_total) }}</td>
                        </tr>
                        <tr>
                            <td align="right" style="padding-right: 8px">Commssion Total</td>
                            <td>${{ number_format($commission_total) }}</td>
                        </tr>
                    </table>
                </div>

            </div>

        @endforeach

    </body>

</html>
