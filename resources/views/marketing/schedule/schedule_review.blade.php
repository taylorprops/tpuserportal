@php
$title = 'Marketing Schedule';
$breadcrumbs = [[$title]];
@endphp
<x-app-layout>
    @section('title')
        {{ $title }}
    @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
            :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="pb-12 pt-2"
        x-data="schedule()">

        <div class="max-w-1600-px mx-auto sm:px-6 lg:px-12">

            <div class="">

                <form x-ref="filter_form">

                    <div class="my-6 flex items-end justify-start space-x-4">

                        @foreach (['company', 'recipient'] as $item)
                            <div>
                                <select class="form-element select md"
                                    data-label="{{ ucwords($item) }}"
                                    name="{{ $item }}_id"
                                    @change="get_schedule()">
                                    <option value="">All</option>
                                    @foreach ($settings -> where('category', $item) as $setting)
                                        <option value="{{ $setting -> id }}">{{ $setting -> item }}
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        <div>
                            <button type="button"
                                class="button primary sm"
                                @click="$refs.filter_form.reset(); get_schedule()">Clear</button>
                        </div>

                    </div>

                </form>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-8">

                <div class="h-screen-80 mb-12 overflow-auto pb-16 lg:mb-0">

                    <div x-ref="schedule_review_list_div"></div>

                </div>

                <div class="flex flex-col">

                    <div class="lg:h-screen-80 relative lg:overflow-y-auto">

                        <div x-show="show_html || show_file">

                            <div class="absolute top-12 right-12 z-20"><a href="javascript:void(0)"
                                    @click="show_html = false; show_file = false; show_calendar = true;"><i class="fa-duotone fa-circle-xmark fa-3x text-red-600 hover:text-red-500"></i></a>
                            </div>

                            <div class="absolute top-0 z-10 h-full w-full border-4 bg-white"
                                x-show="show_html"
                                x-ref="view_html">
                                <iframe class="view-accepted-iframe"
                                    width="100%"
                                    height="100%"></iframe>
                            </div>

                            <div class="absolute top-0 z-10 h-full w-full bg-white"
                                x-show="show_file">
                                <embed src=""
                                    type="application/pdf"
                                    class="min-h-750-px"
                                    width="100%"
                                    height="100vh"
                                    x-ref="view_file" />
                            </div>

                        </div>

                        <div class="z-10"
                            x-show="show_calendar">
                            <div class="calendar"></div>
                        </div>

                    </div>

                </div>

            </div>


        </div>

    </div>

</x-app-layout>
