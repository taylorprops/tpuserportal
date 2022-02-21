<div class="table-container flex flex-col h-screen">

    <div class="table-div max-h-800-px flex-grow overflow-auto">

        <table class="data-table relative">

            <thead>
                <tr>
                    <th scope="col" class="sticky top-0">Date</th>
                    <th scope="col" class="sticky top-0">Changed By</th>
                    <th scope="col" class="sticky top-0">Changes</th>
                </tr>
            </thead>

            <tbody>

                @foreach($changes as $change)

                    <tr>
                        <td valign="top" class="whitespace-nowrap">{{ date('n/j/Y g:i A', strtotime($change -> created_at)) }}</td>
                        <td valign="top" class="whitespace-nowrap">{{ $change -> user -> name ?? 'System' }}</td>
                        <td class="p-0">
                            @foreach($change -> changes as $change_detail)

                                @php
                                $value_before = $change_detail -> value_before;
                                $value_after = $change_detail -> value_after;

                                if(!$value_before) {
                                    $value_before = '<span class="text-gray-300">-----------</span>' ;
                                }
                                @endphp

                                <div class="mb-2 px-2 py-1 border-b">
                                    <div class="grid grid-cols-8 gap-4">

                                        <div class="col-span-2 font-semibold">{{ $change_detail -> field_name_display }}</div>
                                        <div class="col-span-1 text-xs">From</div>
                                        <div class="col-span-2 font-semibold">{!! $change_detail -> value_before ?? '<span class="text-gray-300">-----------</span>' !!}</div>
                                        <div class="col-span-1 text-xs">To</div>
                                        <div class="col-span-2 font-semibold">{{ $change_detail -> value_after }}</div>
                                    </div>

                                </div>

                            @endforeach
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
