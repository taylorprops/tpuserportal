<div class="w-full lg:w-1000-px mx-auto text-right text-sm text-green-600">Autosaved: <span x-ref="updated_at"></span></div>
<div class="w-full lg:w-1000-px mx-auto border-2 rounded-lg p-4" x-ref="notes_div" id="notes_div">
    {!! $notes -> notes !!}
</div>

<div class="flex justify-around mt-8">
    <button type="button" class="button primary lg" @click="save_notes($el)" x-ref="save_notes_button">Save Notes <i class="fa-light fa-check ml-2"></i></button>
</div>
