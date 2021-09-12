@forelse($licenses as $license)

    <div class="flex justify-start items-end license">
        <div class="m-2 w-24">
            <x-elements.select
            name="license_state[]"
            data-label="State"
            value="{{ $license -> license_state }}"
            :size="'md'">
                <option value=""></option>
                @foreach($states as $state)
                    <option value="{{ $state -> state }}" @if($license -> license_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                @endforeach
            </x-elements.select>
        </div>
        <div class="m-2 w-36">
            <x-elements.input
                name="license_number[]"
                data-label="Number"
                value="{{ $license -> license_number }}"
                :size="'md'"/>
        </div>
        <div class="m-2 w-24">
            <x-elements.button
                class="delete-license-button"
                :buttonClass="'danger'"
                :buttonSize="'md'"
                type="button"
                x-on:click="delete_license($el)">
                <i class="fal fa-times mr-2"></i> Delete
            </x-elements.button>
        </div>

    </div>

@empty
    <div class="text-gray-400"><i class="fal fa-exclamation-triangle mr-2"></i> No Licenses Added</div>
@endforelse
