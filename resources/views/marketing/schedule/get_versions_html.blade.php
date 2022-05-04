<div class="mb-6 flex justify-start items-end space-x-12">
    <div>
        <button type="button" class="button primary md" @click="add_version({{ $event_id }})">Add Version <i class="fa-light fa-plus ml-2"></i></button>
    </div>
    <div>
        <button type="button" class="button primary sm"
        @click="show_deleted_versions = !show_deleted_versions"
        x-show="{{ count($versions -> where('active', false)) }} > 0 "> <span x-show="show_deleted_versions == false">Show Deleted Versions</span><span x-show="show_deleted_versions == true">Show Active Versions</span></button>
    </div>
</div>

<div class="flex space-x-4 max-w-screen overflow-y-hidden h-screen-70">

    @foreach($versions as $version)

        <div class="" @if($version -> active === 1) x-show="show_deleted_versions === false" @elseif($version -> active === 0) x-show="show_deleted_versions === true" @endif>

            <div class="flex justify-around">
                @if($version -> active === 1)
                    @if($version -> accepted_version == false)
                        <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="mark_version_accepted({{ $event_id }}, {{ $version -> id }})">Mark as Accepted Version <i class="fa-light fa-check ml-2"></i></a>
                        <a href="javascript:void(0)" class="text-red-600 hover:text-red-500" @click="delete_version({{ $event_id }}, {{ $version -> id }})">Delete <i class="fa-thin fa-times ml-2"></i></a>
                    @else
                        <div>
                            <span class="text-green-600">Accepted Version <i class="fa-light fa-check ml-2"></i></span>
                        </div>
                    @endif
                @else
                    <a href="javascript:void(0)" class="text-primary hover:text-primary-light" @click="reactivate_version({{ $event_id }}, {{ $version -> id }})">Reactivate Version<i class="fa-light fa-rotate-right ml-2"></i></a>
                @endif

            </div>

            <div class="border rounded p-2 w-500-px h-700-px overflow-y-auto">
                @if($version -> html != '')
                    <div class="w-full h-90-perc">
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
