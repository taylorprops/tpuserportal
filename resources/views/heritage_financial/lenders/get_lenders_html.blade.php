<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr id="sortable_tr">
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('company_name', 'Lender')</th>
                <th scope="col">@sortablelink('account_exec_name', 'Account Exec')</th>
                <th scope="col">@sortablelink('basis_points', 'Basis Points')</th>
                <th scope="col">Range</th>
                <th scope="col">Documents</th>
                <th scope="col">@sortablelink('notes', 'Notes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lenders as $lender)
                <tr>
                    <td>
                        <a href="/heritage_financial/lenders/view_lender/{{ $lender -> uuid }}" class="view-link button primary md" target="_blank">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td class="w-64">{{ $lender -> company_name }}</td>
                    <td>
                        {{ $lender -> account_exec_name }}<br>
                        {{ $lender -> account_exec_phone }}<br>
                        <a href="mailto:{{ $lender -> account_exec_email }}" target="_blank">{{ $lender -> account_exec_email }}</a>
                    </td>
                    <td>{{ $lender -> basis_points }}</td>
                    <td>{{ $lender -> minimum }} - {{ $lender -> maximum }}</td>
                    <td></td>
                    <td>{{ $lender -> notes }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>
