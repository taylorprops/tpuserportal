{{-- blade-formatter-disable --}}
@php
$title = 'Lending Pad and Floify Information';
$breadcrumbs = [
    ['Heritage Financial', ''],
    [$title],
];
@endphp
{{-- blade-formatter-enable --}}
<x-app-layout>
    @section('title') {{ $title }} @endsection
    <x-slot name="header">
        <x-nav.bread-crumbs
        :breadcrumbs="$breadcrumbs"/>
    </x-slot>

    <div class="pb-12 pt-2">

        <div class="max-w-full mx-auto sm:px-6 lg:px-12 pt-4 lg:pt-12">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div>

                    <div class="rounded-lg border">

                        <div class="p-4 border-b text-xl rounded-t-lg bg-primary text-white">Floify</div>

                        <div class="p-4">

                            <div class="py-4">
                                Floify is our online application and document management system.
                            </div>

                            <div class="py-2">
                                Website - <a href="https://app.floify.com" class="default" target="_blank">https://app.floify.com</a>
                            </div>

                            <hr class="my-4">

                            <div class="font-semibold mt-8 mb-4 text-xl">Support</div>

                            <div class="ml-4">

                                Help Center - <a href="https://help.floify.com/hc/en-us" class="default" target="_blank">https://help.floify.com/hc/en-us</a>
                                <br>
                                <br>
                                There is also an online chat option wehn logged on.

                            </div>


                            <div class="font-semibold mb-4 mt-8 text-xl">Training</div>

                            <div class="ml-4">

                                To access the Resource Center log on to Floify and at the bottom of the page you will see a link for "Help". Hover over that to see the options.

                            </div>

                        </div>

                    </div>

                    <div class="rounded-lg border mt-6">

                        <div class="p-4 border-b text-xl rounded-t-lg bg-primary text-white">Marketing Links</div>

                        <div class="p-4">
                            <div class="text-lg font-semibold mb-4">Your Marketing and Online Application Links</div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Profile Link</div>
                                    <div class="ml-4">
                                        <a href="https://heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder ?? 'heritage' }}" class="default" target="_blank">heritagefinancial.com/{{ auth() -> user() -> loan_officer -> folder ?? 'USER' }}</a>
                                    </div>
                                </div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Floify Application Landing Page</div>
                                    <div class="ml-4">
                                        <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder ?? 'heritage' }}.floify.com/" class="default" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder ?? 'USER' }}.floify.com/</a>
                                    </div>
                                </div>

                                <div class="flex justify-start p-2 mb-2 border-b">
                                    <div class="font-bold">Floify Start Application Page</div>
                                    <div class="ml-4">
                                        <a href="https://{{ auth() -> user() -> loan_officer -> floify_folder ?? 'heritage' }}.floify.com/apply-now" class="default" target="_blank">{{ auth() -> user() -> loan_officer -> floify_folder ?? 'USER' }}.floify.com/apply-now</a>
                                    </div>
                                </div>
                        </div>

                    </div>

                </div>

                <div class="rounded-lg border mt-6 lg:mt-0">

                    <div class="p-4 border-b text-xl rounded-t-lg bg-primary text-white">Lending Pad</div>

                    @if(auth() -> user() -> level != 'loan_officer')
                        <div class="mt-4 p-2 bg-blue-50 rounded text-center">
                            Install the Chrome Extension
                            <a href="https://chrome.google.com/webstore/detail/lending-pad-add-on/afocimphagojdkhkhhgokfhdhnlialkf?hl=en&authuser=0" target="_blank" class="button primary sm ml-8">Get Extension <i class="fal fa-arrow-right ml-2"></i></a>
                        </div>
                    @endif

                    <div class="p-4">

                        <div class="py-4">
                            Lending Pad is our Loan Origination Software.
                        </div>

                        <div class="py-2">
                            Website - <a href="https://prod.lendingpad.com/heritage-financial/login" class="default" target="_blank">https://prod.lendingpad.com/heritage-financial/login</a>
                        </div>

                        <hr class="my-4">

                        <div class="font-semibold mb-4 mt-8 text-xl">Support</div>

                        <div class="ml-4">

                            <div class="font-semibold mb-3">Customer Success / Support Desk</div>

                            <div>
                                You may still have questions you need support with.  Here is their contact information:
                            </div>

                            <ul class="list-disc list-inside my-4 ml-4">
                                <li>If you are inside of a loan file - select the help icon  on upper right corner of the screen to the right of calendar feature</li>
                                <li>Email <a href="mailto:support@lendingpad.com" class="default" target="_blank">support@lendingpad.com</a> </li>
                                <li>Chat option at the right bottom corner of the application for quick how-to's</li>
                                <li>Support menu, "Create New Ticket" option for general questions</li>
                                <li>Real-time support options, available with a support plan purchase
                                    <ul class="list-disc list-inside">
                                        <li>Call 202-796-2790 option 2</li>
                                        <li>Screen share, pre-scheduled training and support sessions are available under Support menu</li>
                                    </ul>
                                </li>
                            </ul>

                        </div>


                        <div class="font-semibold mb-4 mt-8 text-xl">Training</div>

                        <div class="ml-4">

                            <div class="font-semibold mb-3">Join Free Live Training Classes Every Monday </div>

                            <div>
                                You will be auto-enrolled in these training classes depending on your user role.  The sign up links are also available under <a href="https://prod.lendingpad.com/web/?#/support" class="default" target="_blank">Support</a> / <a href="https://prod.lendingpad.com/web/?#/support/training" class="default" target="_blank">Training page</a>.  Ask questions and practice hands-on.  You will learn how easy it is to originate in LendingPad.
                            </div>


                            <div class="font-semibold my-3">YouTube Videos</div>

                            <div>
                                You may have questions on many of the how-to's for both system administrators as well as loan officers, processors.  Watch it at your own time, pause the video if needed to follow along.
                            </div>

                            <ul class="list-disc list-inside my-4 ml-4">
                                <li>Subscribe to Lending Pad's YouTube channel <a href="https://www.youtube.com/channel/UCLHJssI1cOLeHNNnSKkQbDw/videos" class="default" target="_blank">YouTube Channel</a></li>
                                <li><a href="https://www.youtube.com/watch?v=8JhK7x_c0bs" class="default" target="_blank">How to take an application</a></li>
                                <li><a href="https://www.youtube.com/watch?v=UCcluykgDPo" class="default" target="_blank">How to pull a credit report</a></li>
                                <li><a href="https://www.youtube.com/watch?v=DZVM3OyDd1k" class="default" target="_blank">How to run AUS</a></li>
                                <li><a href="https://www.youtube.com/watch?v=1ZdEI24liB8" class="default" target="_blank">How to issue a pre-approval Letter</a></li>
                                <li><a href="https://www.youtube.com/watch?v=1ZdEI24liB8" class="default" target="_blank">How to use document management features</a></li>
                                <li><a href="https://www.youtube.com/watch?v=zxn6lIs3gCw" class="default" target="_blank">How to use support center</a></li>
                            </ul>

                            <div class="font-semibold mb-3">Knowledge Base</div>

                            <div>
                                Search by keywords, related articles will show up in LendingPad <a href="https://www.lendingpad.com/kb" class="default" target="_blank">Knowledge Base</a>, available under Support menu.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
