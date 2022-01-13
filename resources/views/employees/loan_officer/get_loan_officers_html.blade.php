<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $employees -> links() !!}
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr>
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('last_name', 'Name')</th>
                <th scope="col">@sortablelink('emp_position', 'Position')</th>
                <th scope="col">@sortablelink('email', 'Email')</th>
                <th scope="col">@sortablelink('cell_phone', 'Phone')</th>
                <th scope="col">Licensed In</th>
                <th scope="col">@sortablelink('active', 'Active')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                @php
                if($employee -> emp_position == 'loan_officer') {
                    $emp_position = 'Loan Officer';
                } else {
                    $emp_position = ucwords($employee -> emp_position);
                }
                $licenses = $employee -> licenses -> pluck('license_state') -> toArray();
                $licenses = implode(', ', $licenses);
                @endphp
                <tr>
                    <td>
                        <a href="/employees/loan_officer/loan_officer_view/{{ $employee -> id }}" class="view-link button primary md">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td>{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td>{{ $emp_position }}</td>
                    <td>{{ $employee -> email }}</td>
                    <td>{{ $employee -> phone }}</td>
                    <td>{{ $licenses }}</td>
                    <td class="text-center">
                        @if($employee -> active == 'yes')
                            <div class="inline-block text-white py-2 px-4 rounded-lg bg-success">
                                <i class="fal fa-check mr-2"></i> Yes
                            </div>
                        @else
                            <div class="inline-block text-white py-2 px-4 rounded-lg bg-danger">
                                <i class="fal fa-minus mr-2"></i> No
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $employees -> links() !!}
</div>
