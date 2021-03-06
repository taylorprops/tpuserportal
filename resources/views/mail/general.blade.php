@component('mail::layout')
    {{-- blade-formatter-disable --}}
    @php
    $company = $message['company'];
    if ($company != 'Taylor Properties' && $company != 'Heritage Financial') {
        $company = 'Taylor Properties';
    }
    @endphp
{{-- blade-formatter-enable --}}
    @slot('header')
        <tr>
            <td class="header">
                <a href="{{ config('app.url') }}" style="display: inline-block;">
                    <img class="email-header-logo" src="{{ config('app.url') }}/images/logo/logo_email_{{ str_replace(' ', '', $company) }}.png">
                </a>
            </td>
        </tr>
    @endslot

    <!-- Email Body -->
    {!! $message['body'] !!}


    @slot('footer')
        <tr>
            <td>
                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="content-cell" align="center">
                            © {{ date('Y') }} {{ $company }} @lang('All rights reserved.')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endslot
@endcomponent
