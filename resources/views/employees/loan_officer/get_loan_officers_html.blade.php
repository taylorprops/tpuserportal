<div class="flex justify-between flex-wrap mb-2 pagination-div">
    <div>
        <button type="button" class="button primary md" @click="email_modal = true; show_recipients_added()" :disabled="!show_email_option"><i class="fad fa-envelope mr-2"></i> Email
            Selected Loan Officers</button>
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
                <th scope="col">@sortablelink('last_name', 'Name')</th>
                <th scope="col">@sortablelink('emp_position', 'Position')</th>
                <th scope="col">@sortablelink('email', 'Email')</th>
                <th scope="col">@sortablelink('cell_phone', 'Phone')</th>
                <th scope="col">Licensed In</th>
                <th scope="col">@sortablelink('active', 'Active')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                {{-- blade-formatter-disable --}}
                @php
                    if ($employee -> emp_position == 'loan_officer') {
                        $emp_position = 'Loan Officer';
                    } else {
                        $emp_position = ucwords($employee -> emp_position);
                    }
                    $licenses = $employee -> licenses -> pluck('license_state') -> toArray();
                    $licenses = implode(', ', $licenses);
                @endphp
{{-- blade-formatter-enable --}}
                <tr>
                    <td class="w-14">
                        <div class="flex justify-around items-center">
                            <input type="checkbox" class="form-element checkbox success md recipient-checkbox" data-name="{{ $employee -> fullname }}"
                                data-email="{{ $employee -> email }}" @click="show_email_button()">
                        </div>
                    </td>
                    <td>
                        <a href="/employees/loan_officer/loan_officer_view/{{ $employee -> id }}" class="view-link button primary md">View <i
                                class="fal fa-arrow-right ml-2"></i></a>
                    </td>
                    <td>{{ $employee -> last_name.', '.$employee -> first_name }}</td>
                    <td>{{ $emp_position }}</td>
                    <td><a href="mailto:{{ $employee -> email }}" target="_blank">{{ $employee -> email }}</a></td>
                    <td>{{ $employee -> phone }}</td>
                    <td>{{ $licenses }}</td>
                    <td>
                        @if ($employee -> active == 'yes')
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

<div class="flex justify-end mt-2 pagination-div">
    {!! $employees -> links() !!}
</div>
