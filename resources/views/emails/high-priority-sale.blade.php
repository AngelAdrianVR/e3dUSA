@component('mail::message')
# {{ $greeting }}

{{ $intro }}

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent