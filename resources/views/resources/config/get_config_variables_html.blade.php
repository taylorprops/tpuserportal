<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $configs -> links() !!}
</div>

<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                @php $th_classes = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'; @endphp
                <th scope="col" class="{{ $th_classes }}">@sortablelink('config_key', 'Key')</th>
                <th scope="col" class="{{ $th_classes }}">Value</th>
                <th scope="col" class="{{ $th_classes }}">@sortablelink('config_type', 'Type')</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($configs as $config)
                @php
                $td_classes = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                @endphp
                <tr>
                    <td class="{{ $td_classes }}">{{ $config -> config_key }}</td>
                    <td class="{{ $td_classes }}">
                        <textarea class="form-element textarea md config-input config-value w-full"
                        rows="{{ (strlen($config -> config_value) / 110) }}"
                        data-id="{{ $config -> id }}"
                        data-field="config_value"
                        >{{ $config -> config_value }}</textarea>
                    </td>
                    <td class="{{ $td_classes }}">
                        @php
                        $string = '';
                        $array = '';
                        if($config -> value_type == 'string') {
                            $string = 'selected';
                        } else {
                            $array = 'selected';
                        }
                        @endphp
                        <select class="form-element select md config-input config-key"
                        data-id="{{ $config -> id }}"
                        data-field="value_type"
                        >
                            <option value="string" {{ $string }}>String</option>
                            <option value="array" {{ $array }}>Array</option>
                        </select>
                        {{ $config -> config_type }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $configs -> links() !!}
</div>
