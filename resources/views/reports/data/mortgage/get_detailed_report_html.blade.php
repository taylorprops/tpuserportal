<div class="">

    <div
    x-data="table({
        'container': $refs.container,
        'data_url': '/reports/mortgage/get_detailed_report_data',
        'length': '10',
        'search': false,
        'button_export': true,
        'sort_by': 'settlement_date',
        'form_id': 'detailed_report_form'
    })">

        <div x-ref="container"></div>

    </div>

</div>
