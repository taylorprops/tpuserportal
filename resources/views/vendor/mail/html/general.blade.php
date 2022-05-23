@component('mail.general')

{{ $header }}

{{-- Body --}}
{{ $slot }}

{{ $footer }}

@endcomponent
