@component('mail::layout')
{{-- Header --}}
@slot('header')

<tr>
    <td class="header">
    <a href="{{ config('app.url') }}" style="display: inline-block;">
        <img class="email-header-logo" src="https://tpuserportal.com/images/logo/logo_email_{{ str_replace(' ', '', $user -> company) }}.png">
    </a>
    </td>
    </tr>
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} Taylor Properties. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
