
<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $agents -> links() !!}
</div>

<div class="table-div">

    <table>

        <thead>
            <tr>
                <th scope="col">@sortablelink('last_name', 'Name')</th>
                <th scope="col">@sortablelink('street', 'Address')</th>
                <th scope="col">@sortablelink('email', 'Email')</th>
                <th scope="col">@sortablelink('cell_phone', 'Cell Phone')</th>
                <th scope="col">@sortablelink('start_date', 'Start Date')</th>
                <th scope="col">@sortablelink('company', 'Company')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agents as $agent)
                <tr>
                    <td>{{ $agent -> last_name.', '.$agent -> first_name }}</td>
                    <td>{{ $agent -> city.', '.$agent -> state.' '.$agent -> zip }}</td>
                    <td><a href="mailto:{{ $agent -> email }}" class="text-primary-light hover:text-primary" target="_blank">{{ $agent -> email }}</a></td>
                    <td><a href="tel:{{ $agent -> cell_phone }}" class="text-primary-light hover:text-primary" target="_blank">{{ $agent -> cell_phone }}</a></td>
                    <td>{{ date('m/d/Y', strtotime($agent -> start_date)) }}</td>
                    <td>{{ $agent -> company }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $agents -> links() !!}
</div>
