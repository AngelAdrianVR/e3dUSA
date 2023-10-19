@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($products as $product)
@php
 $raw_material = App\Models\RawMaterial::find($product->raw_material_id);   
@endphp
<li><strong>{{ $raw_material->name }} ({{ $raw_material->storages[0]->quantity }} pzs.)</strong>. Punto de reposición: {{ $raw_material->min_quantity }}</li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent