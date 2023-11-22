@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($branches as $branch)
<li><strong>{{ $branch->name }}</strong></li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent