@foreach($credit_cards as $credit_card)

    <div class="grid grid-cols-12 border-b border-t mb-2 p-2 rounded text-sm">
        <div class="col-span-1 place-self-start">
            <i class="fab fa-cc-{{ strtolower($credit_card -> issuer) }} fa-2x text-primary"></i>
        </div>
        <div class="col-span-3 place-self-center">
            xxxx-{{ $credit_card -> last_four }}
        </div>
        <div class="col-span-3 place-self-center">
            {{ $credit_card -> first.' '.$credit_card -> last }}
        </div>
        <div class="col-span-2 place-self-center">
            {{ $credit_card -> expire }}
        </div>
        <div class="col-span-2 place-self-center">
            @if($credit_card -> default == 'yes')
                <span class="text-xs text-success"><i class="fad fa-check-circle mr-2"></i> Default</span>
            @else
                <button class="button primary sm"
                @click="set_default_credit_card({{ $credit_card -> profile_id }}, {{ $credit_card -> payment_profile_id }})">Set As Default</button>
            @endif
        </div>
        <div class="col-span-1 place-self-end">
            @if($credit_card -> default == 'no')
            <button class="button danger sm no-text"
            id="delete_card_{{ $credit_card -> id }}"
            x-on:click="show_delete_credit_card('delete_card_{{ $credit_card -> id }}', {{ $credit_card -> profile_id }}, {{ $credit_card -> payment_profile_id }})">
                <i class="fal fa-times"></i>
            </button>
            @endif
        </div>
    </div>

@endforeach
