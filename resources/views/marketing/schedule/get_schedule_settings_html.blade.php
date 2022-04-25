
@foreach($settings as $setting)

    @php
    if($type == 'categories') {
        $value = $setting -> category;
    } else if($type == 'mediums') {
        $value = $setting -> medium;
    } else if($type == 'companies') {
        $value = $setting -> company;
    }
    @endphp
    <div class="flex justify-between p-2 my-2 border-b w-full group">
        <div>
            <input type="text" class="editor-inline p-2" {{-- name="item_name" data-type="{{ $type }}" data-id="{{ $setting -> id }}" --}} value="{{ $value }}"
            @blur="save_edit_item('{{ $type }}', {{ $setting -> id }}, $el.value)">
        </div>
        <div class="mr-4">
            <button type="button" class="button danger md no-text" @click="settings_show_delete_item('{{ $type }}', '{{ $setting -> id }}')"><i class="fa-duotone fa-xmark fa-xl"></i></button>
        </div>
    </div>

@endforeach
