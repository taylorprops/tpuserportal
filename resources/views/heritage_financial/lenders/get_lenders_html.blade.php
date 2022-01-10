<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr id="sortable_tr">
                @if(auth() -> user() -> level != 'loan_officer')
                <th width="100" scope="col"></th>
                @endif
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
                    @if(auth() -> user() -> level != 'loan_officer')
                    <td>
                        <a href="/heritage_financial/lenders/view_lender/{{ $lender -> uuid }}" class="view-link button primary md" target="_blank">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    @endif
                    <td class="w-64">{{ $lender -> company_name }}</td>
                    <td>
                        {{ $lender -> account_exec_name }}<br>
                        {{ $lender -> account_exec_phone }}<br>
                        <a href="mailto:{{ $lender -> account_exec_email }}" target="_blank">{{ $lender -> account_exec_email }}</a>
                    </td>
                    <td class="w-32">{{ $lender -> basis_points }}</td>
                    <td class="w-32">{{ $lender -> minimum }} - {{ $lender -> maximum }}</td>
                    <td>
                        @foreach($lender -> documents as $doc)
                            <div>
                                <a href="{{ $doc -> file_location_url }}" target="_blank">{{ $doc -> file_name }}</a>
                            </div>
                        @endforeach
                    </td>
                    <td>
                        <div class="max-h-28 overflow-y-auto">
                            {!! nl2br($lender -> notes) !!}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>
