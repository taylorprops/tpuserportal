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
    Hello {{ $user -> first_name }}!<br><br>
    You are receiving this email because an account was set up for you by {{ $user -> company }}. Please click the link below to set up your account.<br><br>
    <div style="width: 100%; text-align: center; padding: 10px">
        <a class="button button-primary" target="_blank" href="{{ $user -> registration_link }}">Register Account</a>
    </div>
    <div style="margin-top: 15px; font-size: 10px">
    If the above link is not working you can also copy and paste the following into your browser: {{ $user -> registration_link }}
    </div>
</div>


{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ $user -> company }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent

