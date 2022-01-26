<x-modals.modal :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-2/3 xl:w-2/5'" :modalTitle="$title" :modalId="'email_modal'" x-show="email_modal">

    <form id="email_list_form">

        <div class="flex items-start mb-3">
            <div class="w-24 text-sm text-right pr-4">To:</div>
            <div class="flex-grow border border-gray-300 rounded-sm p-2 max-h-100-px overflow-y-auto shadow-sm" x-ref="recipients_added"></div>
        </div>
        <div class="flex items-center mb-3">
            <div class="w-24 text-sm text-right pr-4">Subject:</div>
            <div class="flex-grow">
                <input type="text" class="form-element input md" name="subject" id="subject" placeholder="Subject">
            </div>
        </div>
        <div class="flex items-center mb-3">
            <div class="w-24 text-sm text-right pr-4">Attach:</div>
            <div class="flex-1">
                <input type="file" class="form-element input md" name="attachments[]" id="attachments" multiple
                @change="show_file_names($el);">
            </div>
        </div>
        <div class="flex items-start">
            <div class="w-24 text-sm text-right pr-4">Message:</div>
            <div class="flex-grow">
                <textarea id="message" name="message"></textarea>
            </div>
        </div>
        <div class="flex justify-around pt-8">
            <button type="button" class="button primary lg" @click="send_email($el, '{{ $company }}')">
                Send Email <i class="fa fa-share ml-2"></i>
            </button>
        </div>

    </form>

</x-modals.modal>
