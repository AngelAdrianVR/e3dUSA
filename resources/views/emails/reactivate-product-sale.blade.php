@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($products as $product)
{{-- <li><strong>{{ $product }}</strong></li> --}}
@endforeach

{{$products}}

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent