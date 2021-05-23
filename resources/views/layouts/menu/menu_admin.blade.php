

<a href="/dashboard" class="group flex items-center text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-t focus:bg-gray-700">
    <div class="bg-gray-700 p-1 rounded mr-2">
        <i class="fad fa-tachometer-alt"></i>
    </div>
    Dashboard
</a>


<div x-data="{ open_transactions: false }" class="w-full">

    <div class="cursor-pointer text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-t focus:bg-gray-700" @click="open_transactions = true">

        <div class="flex justify-between items-center w-full">

            <div class="flex justify-start items-center">
                <div class="bg-gray-700 p-1 rounded mr-2">
                    <i class="fad fa-sign"></i>
                </div>
                Tranactions
            </div>
            <div>
                <i class="fal fa-angle-right text-gray-300 fa-lg" :class="{ '' : !open_transactions, 'fa-rotate-90' : open_transactions }"></i>
            </div>
        </div>

    </div>


    <!-- Expandable link section, show/hide based on state. -->
    <div class="space-y-1 bg-gray-700 rounded-b" x-show="open_transactions" @click.away="open_transactions = false">

        <a href="#" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
            <i class="fal fa-eye mr-2"></i> View Transactions
        </a>

        <a href="#" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
            <i class="fal fa-plus mr-2"></i> Listing
        </a>

        <a href="#" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
            <i class="fal fa-plus mr-2"></i> Add Contract
        </a>

        <a href="#" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
            <i class="fal fa-plus mr-2"></i> Add Referral
        </a>

        <div x-data="{ open_transactions_admin: false }" class="w-full">

            <div class="cursor-pointer w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300" @click="open_transactions_admin = true">

                <div class="flex justify-between items-center w-full">

                    <div class="flex justify-start items-center">
                        <i class="fad fa-globe mr-2"></i> Admin
                    </div>
                    <div>
                        <i class="fal fa-angle-right text-gray-300" :class="{ '' : !open_transactions_admin, 'fa-rotate-90' : open_transactions_admin }"></i>
                    </div>
                </div>

            </div>


            <!-- Expandable link section, show/hide based on state. -->
            <div class="space-y-1 bg-gray-700 rounded-b pl-8" x-show="open_transactions_admin" @click.away="open_transactions_admin = false">

                <a href="/doc_management/admin/forms" class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md text-white hover:text-gray-300">
                    Forms
                </a>

            </div>

        </div>

    </div>

</div>





<a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md" x-state-description="undefined: &quot;bg-gray-900 text-white&quot;, undefined: &quot;text-gray-300 hover:bg-gray-700 hover:text-white&quot;">
    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" x-state-description="undefined: &quot;text-gray-300&quot;, undefined: &quot;text-gray-400 group-hover:text-gray-300&quot;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    Team
</a>

<a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md" x-state-description="undefined: &quot;bg-gray-900 text-white&quot;, undefined: &quot;text-gray-300 hover:bg-gray-700 hover:text-white&quot;">
    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" x-state-description="undefined: &quot;text-gray-300&quot;, undefined: &quot;text-gray-400 group-hover:text-gray-300&quot;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
    </svg>
    Projects
</a>

<a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md" x-state-description="undefined: &quot;bg-gray-900 text-white&quot;, undefined: &quot;text-gray-300 hover:bg-gray-700 hover:text-white&quot;">
    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" x-state-description="undefined: &quot;text-gray-300&quot;, undefined: &quot;text-gray-400 group-hover:text-gray-300&quot;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    Calendar
</a>

<a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md" x-state-description="undefined: &quot;bg-gray-900 text-white&quot;, undefined: &quot;text-gray-300 hover:bg-gray-700 hover:text-white&quot;">
    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" x-state-description="undefined: &quot;text-gray-300&quot;, undefined: &quot;text-gray-400 group-hover:text-gray-300&quot;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
    </svg>
    Documents
</a>

<a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md" x-state-description="undefined: &quot;bg-gray-900 text-white&quot;, undefined: &quot;text-gray-300 hover:bg-gray-700 hover:text-white&quot;">
    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-6 w-6" x-state-description="undefined: &quot;text-gray-300&quot;, undefined: &quot;text-gray-400 group-hover:text-gray-300&quot;" x-description="Heroicon name: outline/chart-bar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    Reports
</a>
