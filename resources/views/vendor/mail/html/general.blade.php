@component('mail.general')

{{ $header }}

@endcomponent

{{-- Body --}}
{{ $slot }}

{{ $footer }}

@endcomponent
