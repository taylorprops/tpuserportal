<div class="d-flex justify-content-center mb-2 pagination-div">
    {{ $users -> onEachSide(1) -> links() }}
</div>

<div class="shadow border-b border-gray-200 sm:rounded-lg">

    <table class="w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr id="sortable_tr">
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('last_name', 'User')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('group', 'Group')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('level', 'Level')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('email', 'Email')</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">@sortablelink('active', 'Active')</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
                <tr>
                    <td class="p-2 text-gray-500">{{ $user -> last_name }}, {{ $user -> first_name }}</td>
                    <td class="p-2 text-gray-500">{{ ucwords(str_replace('_', ' ', $user -> group)) }}</td>
                    <td class="p-2 text-gray-500">{{ ucwords(str_replace('_', ' ', $user -> level)) }}</td>
                    <td class="p-2 text-gray-500">{{ $user -> email }}</td>
                    <td class="p-2 text-gray-500">{{ ucwords($user -> active) }}</td>
                    <td><button type="button" class="button primary sm" @click="confirm_send_welcome_email('{{ $user -> id }}', '{{ $user -> name }}')"><i class="fa fa-envelope mr-2"></i> Send Welcome Email</button></td>
                    <td><button type="button" class="button primary sm" @click="confirm_reset_password('{{ $user -> id }}', '{{ $user -> name }}')"><i class="fa fa-key mr-2"></i> Reset Password</button></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {{ $users -> onEachSide(1) -> links() }}
</div>
