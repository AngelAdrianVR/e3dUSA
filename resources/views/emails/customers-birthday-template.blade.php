@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@component('mail::panel')
{!! nl2br(e($contactList)) !!} <!-- Convertir las nuevas líneas a <br> en HTML -->
@endcomponent

{{ $salutation }}

@endcomponent
