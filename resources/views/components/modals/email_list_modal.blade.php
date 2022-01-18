<x-modals.modal :modalWidth="'w-full sm:w-11/12 md:w-3/4 lg:w-2/5'" :modalTitle="'Email Lenders'" :modalId="'email_modal'" x-show="email_modal">

    <div class="flex items-start">
        <div class="w-24 text-sm text-right pr-4">To:</div>
        <div class="flex-grow border rounded p-2 max-h-100-px overflow-y-auto" x-ref="recipients_added"></div>
    </div>
    <div class="flex items-start my-3">
        <div class="w-24 text-sm text-right pr-4">Subject:</div>
        <div class="flex-grow">
            <input type="text" class="form-element input md" name="subject" id="subject" placeholder="Subject" x-ref="subject">
        </div>
    </div>
    <div class="flex items-start">
        <div class="w-24 text-sm text-right pr-4">Message:</div>
        <div class="flex-grow">
            <textarea id="message" name="message" x-ref="message"></textarea>
        </div>
    </div>
    <div class="flex justify-around pt-8">
        <button type="button" class="button primary lg" @click="send_email($el)">
            Send Email <i class="fa fa-share ml-2"></i>
        </button>
    </div>

</x-modals.modal>
