<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-primary-darker to-primary-dark">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-3xl mt-6 mx-2 p-6 sm:p-10 bg-white shadow-md overflow-hidden sm:rounded bg-opacity-10">
        {{ $slot }}
    </div>
</div>
