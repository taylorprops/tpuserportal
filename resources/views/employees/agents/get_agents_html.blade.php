@foreach($agents as $agent)
    <tr>
        <td></td>
        <td>{{ $agent -> full_name }}</td>
        <td>{{ $agent -> cell_phone }}</td>
        <td>{{ $agent -> email }}</td>
    </tr>
@endforeach
