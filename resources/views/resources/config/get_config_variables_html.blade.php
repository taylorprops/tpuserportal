<div class="d-flex justify-content-center mb-2 pagination-div">
    {!! $configs -> links() !!}
</div>

<div class="table-div">

    <table class="data-table">

        <thead>
            <tr>
                <th scope="col">@sortablelink('config_key', 'Key')</th>
                <th scope="col">Value</th>
                <th scope="col">@sortablelink('config_type', 'Type')</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($configs as $config)
                <tr>
                    <td>
                        <input class="form-element input md config-input config-value w-full" rows="3" data-id="{{ $config -> id }}" data-field="config_key"
                            value="{{ $config -> config_key }}">
                    </td>
                    <td>
                        <textarea class="form-element textarea md config-input config-value w-full" rows="1{{-- {{ (strlen($config -> config_value) / 110) }} --}}" data-id="{{ $config -> id }}" data-field="config_value">{{ $config -> config_value }}</textarea>
                    </td>
                    <td>
                        {{-- blade-formatter-disable --}}
                        @php
                            $string = '';
                            $array = '';
                            if ($config -> value_type == 'string') {
                                $string = 'selected';
                            } else {
                                $array = 'selected';
                            }
                        @endphp
{{-- blade-formatter-enable --}}
                        <select class="form-element select md config-input config-key" data-id="{{ $config -> id }}" data-field="value_type">
                            <option value="string" {{ $string }}>String</option>
                            <option value="array" {{ $array }}>Array</option>
                        </select>
                        {{ $config -> config_type }}
                    </td>
                    <td>
                        <button type="button" class="button danger md no-text" @click="config_delete({{ $config -> id }}, $el)"><i class="fa-light fa-times"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="d-flex justify-content-center mt-2 pagination-div">
    {!! $configs -> links() !!}
</div>
