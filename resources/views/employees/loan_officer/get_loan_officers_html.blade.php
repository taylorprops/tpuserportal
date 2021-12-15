<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $employees -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                @php $th_classes = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'; @endphp
                <th width="100" scope="col" class="{{ $th_classes }}"></th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('last_name', 'Name')</th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('emp_position', 'Position')</th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('email', 'Email')</th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('cell_phone', 'Phone')</th>
                <th scope="col" class="{{ $th_classes }}">Licensed In</th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('active', 'Active')</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($employees as $employee)
                @php
                if($employee -> emp_position == 'loan_officer') {
                    $emp_position = 'Loan Officer';
                } else {
                    $emp_position = ucwords($employee -> emp_position);
                }
                $licenses = $employee -> licenses -> pluck('license_state') -> toArray();
                $licenses = implode(', ', $licenses);
                $td_classes = 'p-2 text-sm text-gray-500';
                @endphp
                <tr>
                    <td class="{{ $td_classes }}">
                        <a href="/employees/loan_officer/loan_officer_view/{{ $employee -> id }}" class="view-link button primary md" target="_blank">View <i class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td class="{{ $td_classes }}">{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td class="{{ $td_classes }}">{{ $emp_position }}</td>
                    <td class="{{ $td_classes }}">{{ $employee -> email }}</td>
                    <td class="{{ $td_classes }}">{{ $employee -> phone }}</td>
                    <td class="{{ $td_classes }}">{{ $licenses }}</td>
                    <td class="p-2 text-xs text-center">
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
