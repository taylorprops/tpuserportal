<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $employees -> links() !!}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr>
                <th width="100" scope="col"></th>
                <th scope="col">@sortablelink('active', 'Active')</th>
                <th scope="col">@sortablelink('last_name', 'Name')</th>
                <th scope="col">@sortablelink('job_title', 'Position')</th>
                <th scope="col">@sortablelink('email', 'Email')</th>
                <th scope="col">@sortablelink('cell_phone', 'Phone')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>
                        <a href="/employees/in_house/in_house_view/{{ $employee -> id }}" class="view-link button primary md">View</a>
                    </td>
                    <td class="p-2 text-sm text-gray-500 uppercase @if($employee -> active == 'yes') text-green-600 @else text-red-600 @endif">{{ $employee -> active }}</td>
                    <td>{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td>{{ $employee -> job_title }}</td>
                    <td>{{ $employee -> email }}</td>
                    <td>{{ $employee -> phone }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $employees -> links() !!}
</div>
