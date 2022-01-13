@component('mail::message')
@slot('header')

{!! $message !!}

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} Taylor Properties. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
