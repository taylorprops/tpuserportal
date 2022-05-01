<div class="mb-6">
    <button type="button" class="button primary md" @click="add_version({{ $event_id }})">Add Version <i class="fa-light fa-plus ml-2"></i></button>
</div>

<div class="flex flex-wrap space-x-4 w-screen overflow-y-auto">

    @foreach($versions as $version)

        <div>

            <div class="flex justify-between pb-2">
                @if($version -> accepted_version == false)
                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light">Mark as Accepted Version <i class="fa-light fa-check ml-2"></i></a>
                @else
                    <div>
                        <span class="text-green-600">Accepted Version <i class="fa-light fa-check ml-2"></i></span>
                    </div>
                @endif
                <a href="javascript:void(0)" class="text-red-600 hover:text-red-500">Delete <i class="fa-thin fa-times ml-2"></i></a>
            </div>

            <div class="border rounded p-2 w-600-px h-800-px overflow-y-auto">
                @if($version -> html != '')
                    <div class="w-full h-full">
                        <iframe class="version-iframe" width="100%" height="100%">{!! $version -> html !!}</iframe>
                    </div>
                @else
                    @if($version -> file_type == 'pdf')
                        <embed src="{{ $version -> file_url }}" type="application/pdf" class="min-h-750-px" width="100%" height="100vh" />
                    @else
                        <img src="{{ $version -> file_url }}" class="w-full h-auto">
                    @endif
                @endif
            </div>

        </div>

    @endforeach

</div>
