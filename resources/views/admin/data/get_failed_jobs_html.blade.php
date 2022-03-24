<input type="hidden" x-ref="failed_count" value="{{ count($queue_failed_jobs) }}">

@foreach($queue_failed_jobs as $job)

    @php
    $job_name = substr($job -> name, (strrpos($job -> name, '\\') + 1));
    @endphp

    <div class="border-b p-2 text-xs" x-data="{ show_details: false }">

        <div class="grid grid-cols-8 gap-4">
            <div class="flex">
                <div class="mr-4">
                    <input type="checkbox" class="form-element checkbox md job-checkbox" data-id="{{ $job -> id }}" @change="show_buttons()">
                </div>
                <a href="javascript:void(0)" @click="show_details = true" x-show="show_details === false">View Details <i class="fal fa-arrow-right ml-2"></i></a>
                <a href="javascript:void(0)" @click="show_details = false" x-show="show_details === true">Hide Details <i class="fal fa-arrow-down ml-2"></i></a>
            </div>
            <div>
                <div class="font-semibold">{{ $job_name }}</div>
                <div>{{ $job -> queue }}</div>
            </div>
            <div>
                Attempts:<br>
                {{ $job -> attempt }}
            </div>
            <div>
                Start:<br>
                {{ $job -> started_at }}
            </div>
            <div>
                End:<br>
                {{ $job -> finished_at }}
            </div>
            <div class="col-span-2">
                Exception:<br>
                {{ $job -> exception_class }}
            </div>
        </div>

        <div class="grid grid-cols-6 rounded bg-gray-50 p-2 mt-4" x-show="show_details === true" x-transition>
            <div class="mb-4">Excpetion Class</div>
            <div class="col-span-5">{{ $job -> exception_class }}</div>
            <div class="mb-4">Excpetion Message</div>
            <div class="col-span-5">{{ $job -> exception_message }}</div>
            <div class="mb-4">Excpetion</div>
            <div class="col-span-5 max-h-300-px overflow-auto mb-4">{!! preg_replace('/(#[0-9]+)/', '<br>$1', $job -> exception) !!}</div>
            <div class="">Data</div>
            <div class="col-span-5 max-h-300-px overflow-auto mb-4">{{ $job -> data }}</div>
        </div>

    </div>

@endforeach
