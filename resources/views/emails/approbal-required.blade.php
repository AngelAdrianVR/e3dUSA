@component('mail::message')
# {{ $greeting }}

{{ $intro }}

{{ $outro }}

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent
