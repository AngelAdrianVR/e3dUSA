@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($products as $product)
<li><strong>{{ $product->catalogProduct->name }}</strong> perteneciente a <strong>{{ $product->company->business_name }}</strong></li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent