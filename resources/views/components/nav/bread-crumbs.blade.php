<nav class="flex mt-4 text-xs md:text-base w-full" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center pb-4 space-x-1 md:space-x-4 overflow-x-auto whitespace-nowrap">
        <li>
            <div>
                <a href="/dashboard" class="text-gray-400 hover:text-gray-500 flex items-center">
                    <!-- Heroicon name: solid/home -->
                    <i class="fad fa-tachometer-alt-fast mr-3"></i>
                    Dashboard
                </a>
            </div>
        </li>

        @foreach($breadcrumbs as $breadcrumb)
            <li>
                <div class="flex items-center">
                    <i class="fa fa-chevron-right text-gray-400 fa-sm mt-0 md:mt-1"></i>
                    <a href="{{ $breadcrumb[1] ?? null }}" class="ml-1 md:ml-4 font-medium @if($loop -> last) text-secondary cursor-auto @else text-gray-400 hover:text-gray-700 @endif">{{ $breadcrumb[0] }}</a>
                </div>
            </li>
        @endforeach

    </ol>
</nav>
