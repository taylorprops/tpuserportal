{{-- blade-formatter-disable --}}
@php

$count_total_loans = count($loans);
$amount_total_loans = 0;
$company_net = 0;

$count_fha_loans = 0;
$amount_fha_loans = 0;
$percent_fha_loans = 0;
$count_va_loans = 0;
$amount_va_loans = 0;
$percent_va_loans = 0;
$count_usda_loans = 0;
$amount_usda_loans = 0;
$percent_usda_loans = 0;
$count_total_gov_loans = 0;
$amount_total_gov_loans = 0;
$percent_total_gov_loans = 0;

$count_conventional_loans = 0;
$amount_conventional_loans = 0;
$percent_conventional_loans = 0;

$count_reverse_loans = 0;
$amount_reverse_loans = 0;
$percent_reverse_loans = 0;

$count_purchase_loans = 0;
$amount_purchase_loans = 0;
$percent_purchase_loans = 0;

$count_refi_loans = 0;
$amount_refi_loans = 0;
$percent_refi_loans = 0;

foreach ($loans as $loan) {
    $loan_type = $loan -> loan_type;
    $loan_amount = $loan -> loan_amount;

    $amount_total_loans += $loan_amount;
    $company_net += $loan -> company_commission;

    if ($loan_type == 'FHA') {
        $count_fha_loans += 1;
        $amount_fha_loans += $loan_amount;
    } elseif ($loan_type == 'VA') {
        $count_va_loans += 1;
        $amount_va_loans += $loan_amount;
    } elseif ($loan_type == 'USDA') {
        $count_usda_loans += 1;
        $amount_usda_loans += $loan_amount;
    } elseif ($loan_type == 'Conventional') {
        $count_conventional_loans += 1;
        $amount_conventional_loans += $loan_amount;
    }

    if ($loan_type == 'FHA' || $loan_type == 'VA' || $loan_type == 'USDA') {
        $count_total_gov_loans += 1;
        $amount_total_gov_loans += $loan_amount;
    }

    if ($loan -> reverse == 'yes') {
        $count_reverse_loans += 1;
        $amount_reverse_loans += $loan_amount;
    }

    if ($loan -> loan_purpose == 'Purchase') {
        $count_purchase_loans += 1;
        $amount_purchase_loans += $loan_amount;
    }

    if (preg_match('/refinance/i', $loan -> loan_purpose)) {
        $count_refi_loans += 1;
        $amount_refi_loans += $loan_amount;
    }
}

$percent_fha_loans = round($count_fha_loans / $count_total_loans, 2) * 100;
$percent_va_loans = round($count_va_loans / $count_total_loans, 2) * 100;
$percent_usda_loans = round($count_usda_loans / $count_total_loans, 2) * 100;
$percent_conventional_loans = round($count_conventional_loans / $count_total_loans, 2) * 100;
$percent_reverse_loans = round($count_reverse_loans / $count_total_loans, 2) * 100;
$percent_purchase_loans = round($count_purchase_loans / $count_total_loans, 2) * 100;
$percent_refi_loans = round($count_refi_loans / $count_total_loans, 2) * 100;
$percent_total_gov_loans = round($count_total_gov_loans / $count_total_loans, 2) * 100;

@endphp
{{-- blade-formatter-enable --}}

<div class="flex justify-around flex-wrap w-full space-x-3">

    <div class="flex bg-blue-50 rounded-lg border shadow">
        <div class="p-2 border-r">Total Count</div>
        <div class="bg-white p-2 rounded-r-lg">{{ $count_total_loans }}</div>
    </div>
    <div class="flex bg-blue-50 rounded-lg border shadow">
        <div class="p-2 border-r">Total Amount</div>
        <div class="bg-white p-2 rounded-r-lg">${{ number_format($amount_total_loans, 2) }}</div>
    </div>
    <div class="flex bg-blue-50 rounded-lg border shadow">
        <div class="p-2 border-r">Company Net</div>
        <div class="bg-white p-2 rounded-r-lg">${{ number_format($company_net, 2) }}</div>
    </div>

</div>

<div class="w-full mt-3">

    <div class="flex flex-col">

        <div class=" w-full">

            <div class="py-2 align-middle inline-block w-full">

                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg w-full">

                    <table class="table-default text-xs th-text-center td-text-center">

                        <thead>
                            <tr>
                                <th class="text-left"></th>
                                <th class="text-left">Count</th>
                                <th class="text-left">Total Amount</th>
                                <th class="text-left">Percent</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="w-32">
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">FHA</div>
                                </td>
                                <td>
                                    {{ $count_fha_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_fha_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_fha_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">VA</div>
                                </td>
                                <td>
                                    {{ $count_va_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_va_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_va_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">USDA</div>
                                </td>
                                <td>
                                    {{ $count_usda_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_usda_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_usda_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td class="font-semibold">
                                    Total Government Loans
                                </td>
                                <td>
                                    {{ $count_total_gov_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_total_gov_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_total_gov_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">Conventional</div>
                                </td>
                                <td>
                                    {{ $count_conventional_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_conventional_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_conventional_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">Reverse</div>
                                </td>
                                <td>
                                    {{ $count_reverse_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_reverse_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_reverse_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">Purchase</div>
                                </td>
                                <td>
                                    {{ $count_purchase_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_purchase_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_purchase_loans }}%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="bg-blue-50 text-center font-semibold rounded-lg p-2">Refis</div>
                                </td>
                                <td>
                                    {{ $count_refi_loans }}
                                </td>
                                <td>
                                    ${{ number_format($amount_refi_loans, 2) }}
                                </td>
                                <td>
                                    {{ $percent_refi_loans }}%
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
