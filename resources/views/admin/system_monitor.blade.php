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

    <div class="pb-12 pt-2"
    x-data="monitor()">

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
                        <div class="flex items-center ml-2 mb-3 pb-2 border-b">
                            <div>
                                <input type="checkbox" class="form-element checkbox lg" data-label="Check All"
                                @change="
                                check_all = ! check_all;
                                checkboxes = document.querySelectorAll('.job-checkbox');
                                [...checkboxes].map((el) => {
                                    el.checked = check_all;
                                });
                                show_buttons();">
                            </div>
                            <div class="ml-4" x-show="show_buttons_div" x-transition>
                                <button class="button primary md" @click="delete_checked($el)">Delete Checked</button>
                            </div>
                        </div>
                        <div class="max-h-600-px overflow-auto" x-ref="failed_jobs_div"></div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
