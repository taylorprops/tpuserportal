<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $employees -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('active', 'Active')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('last_name', 'Name')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('emp_position', 'Position')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('email', 'Email')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('cell_phone', 'Phone')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licensed In</th>
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
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="/employees/loan_officer/loan_officer_view/{{ $employee -> id }}" class="view-link button primary md" target="_blank">View</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase @if($employee -> active == 'yes') text-green-600 @else text-red-600 @endif">{{ $employee -> active }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $emp_position }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee -> email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $employee -> phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $licenses }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $employees -> links() !!}
</div>
