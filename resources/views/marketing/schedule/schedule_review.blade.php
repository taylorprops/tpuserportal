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

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

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

            <div class="h-screen-80 mb-12 overflow-auto pb-16 pr-4 lg:mb-0">

                <div x-ref="schedule_review_list_div"></div>

            </div>


        </div>

    </div>

</x-app-layout>
