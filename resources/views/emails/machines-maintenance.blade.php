@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($machines as $machine)
<li><strong>{{ $machine['name'] }}</strong></li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent