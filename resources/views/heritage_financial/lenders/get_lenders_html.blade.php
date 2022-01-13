<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $lenders -> onEachSide(1) -> links() }}
</div>

<div class=" py-3">
    <button type="button" class="button primary md" @click="email_modal = true;" :disabled="!show_email_option"><i class="fad fa-envelope mr-2"></i> Email Selected Lenders</button>
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr id="sortable_tr">
                @if(auth() -> user() -> level != 'loan_officer')
                <th>
                    <div class="w-12 flex justify-around items-center">
                        <input type="checkbox" class="form-element checkbox success lg" @click="check_all($el.checked); show_email();">
                    </div>
                </th>
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
                        <div class="flex justify-around items-center">
                            <input type="checkbox" class="form-element checkbox success md lender-checkbox"
                            data-company-name="{{ $lender -> company_name }}"
                            data-ae-name="{{ $lender -> account_exec_name }}"
                            data-ae-email="{{ $lender -> account_exec_email }}"
                            @click="show_email()">
                        </div>
                    </td>
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
