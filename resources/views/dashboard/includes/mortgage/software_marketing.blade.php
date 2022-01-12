<div class="rounded-t-lg border-b p-3 text-lg font-semibold">
    Software/Marketing
</div>

<div class="p-2">

    @if(auth() -> user() -> level == 'loan_officer')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    @endif

        <div class="grid grid-rows-2 p-2 rounded bg-blue-50 text-center">

            <div class="">
                <span class="font-semibold text-lg">Lending Pad</span><br>
                Loan Origination Software.
            </div>
            <div class="flex justify-around items-end">
                <a href="/heritage_financial/loan_software" class="button primary lg">Lending Pad Info <i class="fal fa-arrow-right ml-2"></i></a>
            </div>

        </div>

        <div class="grid grid-rows-2 p-2 rounded bg-blue-50 text-center">

            <div class="">
                <span class="font-semibold text-lg">Floify</span><br>
                Online application and document management system.
            </div>
            <div class="flex justify-around items-end">
                <a href="/heritage_financial/loan_software" class="button primary lg">Floify Info <i class="fal fa-arrow-right ml-2"></i></a>
            </div>

        </div>

        @if(auth() -> user() -> level == 'loan_officer')
        <div class="col-span-1 sm:col-span-2">

            <div class="text-lg font-semibold mb-4">Your Marketing and Online Application Links</div>

            <div class="flex justify-start p-2 mb-2 border-b">
                <div class="font-bold">Profile Link</div>
                <div class="ml-4">
                    <a href="https://heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder }}" target="_blank">heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder }}</a>
                </div>
            </div>

            <div class="flex justify-start p-2 mb-2 border-b">
                <div class="font-bold">Floify Application Landing Page</div>
                <div class="ml-4">
                    <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/</a>
                </div>
            </div>

            <div class="flex justify-start p-2 mb-2 border-b">
                <div class="font-bold">Floify Start Application Page</div>
                <div class="ml-4">
                    <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/apply-now" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder }}.floify.com/apply-now</a>
                </div>
            </div>

        </div>
        @endif

    </div>

</div>
