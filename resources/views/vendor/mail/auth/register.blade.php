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
<div style="font-weight: bold; font-size: 16px; margin-bottom: 10px">
    Account Registration
</div>

<div style="color: rgb(104, 101, 101);">
    Hello {{ $user -> first_name }}!<br>
    Registration Link: {{ $user -> registration_link }}
</div>


{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ $user -> company }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent

