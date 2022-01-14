@component('mail::layout')
{{-- Header --}}
{{-- @component('mail::header')

@endcomponent --}}
@slot('header')

<tr>
    <td class="header">
    <a href="{{ config('app.url') }}" style="display: inline-block;">
        <img class="email-header-logo" src="https://tpuserportal.com/images/logo/logo_all_white.png">
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
