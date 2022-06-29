{{-- blade-formatter-disable --}}
@php
$title = $lender -> company_name ?? 'Add Lender';
$breadcrumbs = [
    ['Heritage Financial', ''],
    ['Lenders', '/heritage_financial/lenders'],
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

    <div class="pb-12 pt-2"
    x-data="lender(
        '{{ $lender -> uuid ?? null }}',
    )">

        <div class="max-w-1400-px mx-auto sm:px-6 lg:px-12">

            <div class="">

                <form id="details_form">

                    <div class="w-1300-px mx-auto mt-12">

                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

                            <div class="col-span-1 lg:col-span-3 border-2 rounded-lg p-4">

                                <div class="text-gray-700 font-semibold text-lg"><i class="fad fa-info-circle mr-2"></i> Details</div>

                                <div class="my-4 max-w-100-px">
                                    <select
                                    class="form-element select md"
                                    name="active"
                                    data-label="Active">
                                        <option value="yes" @if($lender && $lender -> active == 'yes') selected @endif>Yes</option>
                                        <option value="no" @if($lender && $lender -> active == 'no') selected @endif>No</option>
                                    </select>
                                </div>

                                @if(!$lender)
                                <div class="text-red-500 text-sm">"Company Name" and "Company Name Short" must match "Contact Name" and  "Short Name" in Lending Pad</div>
                                @endif
                                <div class="grid grid-cols-1 lg:grid-cols-2">
                                    <div class="my-4 max-w-300-px">
                                        <input type="text" class="form-element input md required" name="company_name" data-label="Company Name" value="{{ $lender -> company_name ?? null }}">
                                    </div>
                                    <div class="my-4 max-w-300-px">
                                        <input type="text" class="form-element input md required" name="company_name_short" data-label="Company Name Short" value="{{ $lender -> company_name_short ?? null }}">
                                    </div>
                                </div>

                                <div class="my-4 grid grid-cols-5 gap-4">
                                    <div class="col-span-3">
                                        <input type="text" class="form-element input md" name="company_street" data-label="Street" value="{{ $lender -> company_street ?? null }}">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="text" class="form-element input md" name="company_unit" data-label="Unit" value="{{ $lender -> company_unit ?? null }}">
                                    </div>
                                </div>

                                <div class="my-4 grid grid-cols-4 gap-4">
                                    <div class="col-span-1">
                                        <input type="text" class="form-element input md" id="company_zip" name="company_zip" data-label="Zip" value="{{ $lender -> company_zip ?? null }}"
                                        x-on:keyup="get_location_details('#details_form', '', '#company_zip', '#company_city', '#company_state');">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="text" class="form-element input md" id="company_city" name="company_city" data-label="City" value="{{ $lender -> company_city ?? null }}">
                                    </div>
                                    <div class="col-span-1">
                                        <select
                                        class="form-element select md"
                                        id="company_state"
                                        name="company_state"
                                        data-label="State">
                                            <option value=""></option>
                                            @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($lender && $lender -> company_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="my-4 grid grid-cols-7 gap-4">
                                    <div class="col-span-2">
                                        <input type="text" class="form-element input md" name="account_exec_name" data-label="AE Name" value="{{ $lender -> account_exec_name ?? null }}">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="email" class="form-element input md" name="account_exec_email" data-label="AE Email" value="{{ $lender -> account_exec_email ?? null }}">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="text" class="form-element input md phone" name="account_exec_phone" data-label="AE Phone" value="{{ $lender -> account_exec_phone ?? null }}">
                                    </div>
                                </div>

                                <div class="my-4 grid grid-cols-3 gap-4">
                                    <div>
                                        <input type="text" class="form-element input md" name="basis_points" data-label="Basis Points" value="{{ $lender -> basis_points ?? null }}">
                                    </div>
                                    <div>
                                        <input type="email" class="form-element input md" name="minimum" data-label="Range Minimum" value="{{ $lender -> minimum ?? null }}">
                                    </div>
                                    <div>
                                        <input type="email" class="form-element input md" name="maximum" data-label="Range Maximum" value="{{ $lender -> maximum ?? null }}">
                                    </div>
                                </div>

                                <div class="flex justify-around p-4">
                                    <button type="button" class="button primary md"
                                    @click="save_details($el)">
                                        Save Details <i class="fal fa-check ml-2"></i>
                                    </button>
                                </div>



                            </div>

                            <div class="col-span-1 lg:col-span-2">

                                <div class="border-2 rounded-lg p-4">
                                    <div class="text-gray-700 font-semibold text-lg"><i class="fad fa-copy mr-2"></i> Documents</div>

                                    <div class="mb-8 mt-6">
                                        <div class="text-gray mb-3">Add Documents</div>
                                        <input
                                        type="file"
                                        class="form-element input md"
                                        id="lender_docs"
                                        name="lender_docs"
                                        multiple>
                                    </div>

                                    <div class="mt-12 mb-3">Uploaded Documents</div>
                                    <div class="border rounded-sm p-4">
                                        <div class="docs-div"></div>
                                    </div>

                                </div>

                                <div class="border-2 rounded-lg p-4 mt-8">
                                    <div class="text-gray-700 font-semibold text-lg"><i class="fad fa-sticky-note mr-2"></i> Notes</div>

                                    <div>
                                        <textarea class="form-element textarea md" x-ref="notes" name="notes">{!! $lender -> notes ?? null !!}</textarea>
                                    </div>

                                    <div class="flex justify-around p-4">
                                        <button type="button" class="button primary md"
                                        @click="save_details($el)">
                                            Save Notes <i class="fal fa-check ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <input type="hidden" name="id" value="{{ $lender -> id ?? null }}">
                    <input type="hidden" name="uuid" value="{{ $lender -> uuid ?? null }}">

                </form>

            </div>

        </div>

        <template id="doc_template">
            <div class="flex justify-between items-center border-b pb-2 mb-4 text-sm">
                <div>
                    <a href="%%url%%" target="_blank">%%file_name%%</a>
                </div>
                <div>

                    <button
                    type="button"
                    class="button danger sm no-text"
                    x-on:click="delete_doc(%%id%%)">
                        <i class="fal fa-times"></i>
                    </button>
                </div>
            </div>
        </template>

    </div>

</x-app-layout>
