@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($products as $product)
@php
 $catalog_product = App\Models\CatalogProduct::find($product->catalog_product_id);   
@endphp
<li><strong>{{ $catalog_product->name }}</strong></li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent