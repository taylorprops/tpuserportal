@php
$title = 'System Monitor';
$breadcrumbs = [
    ['Super Admin', ''],
    [$title],
];
@endphp
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="w-full sm:px-6 lg:px-12">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">

                <div class="border rounded-md shadow">

                    <div class="bg-gray-50 p-2 border-b text-lg rounded-t-md">
                        Database Backups
                    </div>

                    <div class="grid grid-cols-2 gap-8 p-4">

                        <div>
                            <div class="flex justify-between font-semibold mb-3">
                                <div>TP User Portal</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class_tp }}"> {{ count($mysql_backups_tp) }} </span>
                            </div>
                            <div class="max-h-200-px overflow-auto">
                                @foreach($mysql_backups_tp as $backup)
                                    <div class="flex border-b p-2 text-xs">
                                        <div class="w-3/4">{{ $backup['file_name'] }}</div>
                                        <div>{{ $backup['size'] }}MB</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between font-semibold mb-3">
                                <div>Heritage Financial</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class_hf }}"> {{ count($mysql_backups_hf) }} </span>
                            </div>
                            <div class="max-h-200-px overflow-auto">
                                @foreach($mysql_backups_hf as $backup)
                                    <div class="flex border-b p-2 text-xs">
                                        <div class="w-3/4">{{ $backup['file_name'] }}</div>
                                        <div>{{ $backup['size'] }}MB</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>

                <div class="border rounded-md shadow">

                    <div class="bg-gray-50 p-2 border-b text-lg rounded-t-md">
                        File Backups
                    </div>

                    <div class="grid grid-cols-2 gap-8 p-4">

                        <div>
                            <div class="flex justify-between font-semibold mb-3">
                                <div>TP User Portal</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class_tp }}"> {{ count($file_backups_tp) }} </span>
                            </div>
                            <div class="max-h-200-px overflow-auto">
                                @foreach($file_backups_tp as $backup)
                                    <div class="flex border-b p-2 text-xs">

                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between font-semibold mb-3">
                                <div>Heritage Financial</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class_hf }}"> {{ count($file_backups_hf) }} </span>
                            </div>
                            <div class="max-h-200-px overflow-auto">
                                @foreach($file_backups_hf as $backup)
                                    <div class="flex border-b p-2 text-xs">

                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>


            </div>


            <div class="mt-12">

                <div class="border rounded-md shadow">

                    <div class="bg-gray-50 p-2 border-b text-lg rounded-t-md">
                        Queued Jobs
                    </div>

                    <div class="p-4">
                        <div class="font-semibold mb-3">
                            Failed Queued Jobs
                        </div>
                        <div class="max-h-600-px overflow-auto">

                            @foreach($queue_failed_jobs as $job)

                                @php
                                $job_name = substr($job -> name, (strrpos($job -> name, '\\') + 1));
                                @endphp

                                <div class="grid grid-cols-5 gap-4 border-b p-2 text-xs">
                                    <div class="">
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
                                    <div class="w-600-px">
                                        <div x-data="{ show_details: false }">
                                            <div>
                                                <a href="javascript: void(0)"
                                                @click="show_details = true">
                                                    Exceptions
                                                    <i class="fal fa-arrow-right ml-2"></i>
                                                </a>
                                            </div>
                                            <div x-show="show_details === false" x-transition>
                                                {{ Str::limit($job -> exception_class, 150, $end = '...') }}
                                            </div>
                                            <div x-show="show_details === true" x-transition>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>Excpetion Class</div>
                                                    <div>{{ $job -> exception_class }}</div>
                                                    <div>Excpetion</div>
                                                    <div>{{ $job -> exception }}</div>
                                                    <div>Excpetion Message</div>
                                                    <div>{{ $job -> exception_message }}</div>
                                                    <div>Data</div>
                                                    <div>{{ $job -> data }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
