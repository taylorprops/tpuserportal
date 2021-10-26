@forelse($licenses as $license)

    <div class="flex justify-start items-end license mb-2">
        <div class="mx-2 w-24">
            <select
            class="form-element select md required"
            name="license_state[]"
            data-label="State"
            value="{{ $license -> license_state }}">
                <option value=""></option>
                @foreach($states as $state)
                    <option value="{{ $state -> state }}" @if($license -> license_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                @endforeach
            </select>
        </div>
        <div class="mx-2 w-36">
            <input
            type="text"
            class="form-element input md required"
            name="license_number[]"
            data-label="Number"
            value="{{ $license -> license_number }}">
        </div>
        <div class="mx-2 w-28 pb-1">
            <button
            type="button"
            class="button danger md delete-license-button"
            x-on:click="delete_license($el)">
                <i class="fal fa-times mr-2"></i> Delete
            </button>
        </div>

    </div>

@empty
    <div class="text-gray-400"><i class="fal fa-exclamation-triangle mr-2"></i> No Licenses Added</div>
@endforelse
