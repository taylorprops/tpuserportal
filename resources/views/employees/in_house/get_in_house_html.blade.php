<div class="flex justify-between flex-wrap mb-2 pagination-div">
    <div>
        <button type="button" class="button primary md" @click="email_modal = true; show_recipients_added()" :disabled="!show_email_option"><i class="fad fa-envelope mr-2"></i> Email Selected Staff</button>
    </div>
    <div class="">
        {!! $employees -> links() !!}
    </div>
</div>



<div class="table-div">

    <table class="data-table">

        <thead>
            <tr>
                <th class="w-14">
                    <div class="w-12 flex justify-around items-center">
                        <input type="checkbox" class="form-element checkbox success lg" @click="check_all($el.checked); show_email_button();">
                    </div>
                </th>
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
                    <td class="w-14">
                        <div class="flex justify-around items-center">
                            <input type="checkbox" class="form-element checkbox success md recipient-checkbox"
                            data-name="{{ $employee -> fullname }}"
                            data-email="{{ $employee -> email }}"
                            @click="show_email_button()">
                        </div>
                    </td>
                    <td>
                        <a href="/employees/in_house/in_house_view/{{ $employee -> id }}" class="view-link button primary md">View</a>
                    </td>
                    <td class="p-2 text-sm text-gray-500 uppercase @if($employee -> active == 'yes') text-green-600 @else text-red-600 @endif">{{ $employee -> active }}</td>
                    <td>{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td>{{ $employee -> job_title }}</td>
                    <td><a href="mailto:{{ $employee -> email }}" target="_blank">{{ $employee -> email }}</a></td>
                    <td>{{ $employee -> phone }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="flex justify-end mt-2 pagination-div">
    {!! $employees -> links() !!}
</div>
