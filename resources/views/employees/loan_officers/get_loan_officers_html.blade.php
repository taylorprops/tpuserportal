<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $loan_officers -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th width="100" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('active', 'Active')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('last_name', 'Name')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('position', 'Position')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('email', 'Email')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('cell_phone', 'Phone')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licensed In</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($loan_officers as $loan_officer)
                @php
                if($loan_officer -> position == 'loan_officer') {
                    $position = 'Loan Officer';
                } else {
                    $position = ucwords($loan_officer -> position);
                }
                $licenses = $loan_officer -> licenses -> pluck('license_state') -> toArray();
                $licenses = implode(', ', $licenses);
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="/employees/loan_officers/loan_officer_view/{{ $loan_officer -> id }}" class="view-link px-4 py-3 bg-primary text-white text-center shadow rounded-md" target="_blank">View</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase @if($loan_officer -> active == 'yes') text-green-600 @else text-red-600 @endif">{{ $loan_officer -> active }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan_officer -> last_name.', '.$loan_officer -> first_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $position }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan_officer -> email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan_officer -> phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $licenses }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $loan_officers -> links() !!}
</div>
