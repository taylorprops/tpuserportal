<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            * {
                font-family: Arial, Helvetica, sans-serif;
            }
            .loan-officer-div {
                border: 1px solid #ccc;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 15px;
            }
            .report-name {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .loan-officer-name {
                font-size: 14px;
                color: #333;
                font-weight: bold;
                margin-bottom: 10px;
            }
            table {
                font-size: 10px
            }
            th {
                border-bottom: 2px solid #ccc;
                padding: 2px;
                font-size: 10px;
                text-align: left;
            }
            td {
                border-bottom: 1px solid #ccc;
                padding: 2px;
            }
            .loans-summary {
                margin-top: 10px;
                font-weight: bold;
                font-size: 11px;
            }
        </style>
    </head>

    <body>

        <div class="report-name">
            {{ $report_name }}
        </div>

        @foreach($loan_officers as $loan_officer)

            <div class="loan-officer-div">

                <div class="loan-officer-name">
                    {{ $loan_officer -> fullname }}
                </div>

                <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                            $agent_name = $loan -> agent_name;
                            if($loan -> agent_company != '') {
                                $agent_name .= '<br>'.$loan -> agent_company;
                            }
                            $loan_amounts_total += $loan -> loan_amount;
                            $commission_total += $loan -> company_commission;
                            @endphp

                            <tr>
                                <td>{!! $borrower !!}</td>
                                <td>{!! $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip !!}</td>
                                <td>{{ $loan -> purpose == 'refi' ? 'Refi' : 'Purchase' }}</td>
                                <td>{{ $loan -> processor -> fullname }}</td>
                                <td>{!! $agent_name !!}</td>
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

                <div class="loans-summary">
                    Total Loans: {{ $loans_count }}<br>
                    Total Loans Amount: ${{ number_format($loan_amounts_total) }}<br>
                    Commssion Total: ${{ number_format($commission_total) }}
                </div>

            </div>

        @endforeach

    </body>

</html>
