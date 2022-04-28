@foreach($items as $item)

    <div class="border-b p-2 mb-2 text-sm">

        <div class="flex flex-col col-span-4">

            <div class="flex justify-between font-semibold bg-gray-100 p-2 rounded-t mb-2">
                <div>
                    {{ $item -> event_date }}
                </div>
                <div>
                    {{ $item -> company -> company }}
                </div>
            </div>
            <div class="grid grid-cols-3 px-2 mb-2">
                <div>
                    {{ $item -> medium -> medium }}
                </div>
                <div>
                    {{ str_replace(',', ', ', $item -> state) }}
                </div>
                <div class="text-right">
                    {{ $item -> recipient -> recipient }}
                </div>
            </div>

            <div class="flex justify-around">

                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_edit_div('{{ $item -> id }}')">Edit</a>
                <div class="mx-2 w-1 border-r"></div>
                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_view_div('{{ $item -> upload_file_type }}', '{{ $item -> upload_file_url }}', '{{ $item -> upload_html }}')">View Ad Final</a>
                <div class="mx-2 w-1 border-r"></div>
                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_view_div('{{ $item -> upload_file_type }}', '{{ $item -> upload_file_url }}', '{{ $item -> upload_html }}')">View Ad Versions</a>
                <div class="mx-2 w-1 border-r"></div>
                <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="show_view_div('{{ $item -> upload_file_type }}', '{{ $item -> upload_file_url }}', '{{ $item -> upload_html }}')">Add Version</a>

            </div>

        </div>



    </div>

@endforeach
