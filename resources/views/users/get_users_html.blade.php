<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $users -> onEachSide(1) -> links() }}
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr id="sortable_tr">
                <th scope="col">@sortablelink('last_name', 'User')</th>
                <th scope="col">@sortablelink('group', 'Group')</th>
                <th scope="col">@sortablelink('level', 'Level')</th>
                <th scope="col">@sortablelink('email', 'Email')</th>
                <th scope="col">@sortablelink('active', 'Active')</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user -> last_name }}, {{ $user -> first_name }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $user -> group)) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $user -> level)) }}</td>
                    <td>{{ $user -> email }}</td>
                    <td>{{ ucwords($user -> active) }}</td>
                    <td>
                        <button type="button" class="button primary sm"
                        @if($user -> active == 'no') disabled @endif
                        @click="confirm_send_welcome_email('{{ $user -> id }}', '{{ $user -> name }}')"><i class="fa fa-envelope mr-2"></i> Send Welcome Email</button>
                    </td>
                    <td>
                        <button type="button" class="button primary sm"
                        @if($user -> active == 'no') disabled @endif
                        @click="confirm_reset_password('{{ $user -> id }}', '{{ $user -> name }}')"><i class="fa fa-key mr-2"></i> Reset Password</button>
                    </td>
                    <td>
                        <a href="{{ route('login_as_user', $user -> id) }}" class="button primary sm"><i class="fa fa-user mr-2"></i> Login As User</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $users -> onEachSide(1) -> links() }}
</div>
