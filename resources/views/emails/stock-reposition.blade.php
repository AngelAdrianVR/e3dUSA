@component('mail::message')
# {{ $greeting }}

{{ $intro }}

@foreach ($products as $product)
{{-- @php
 $raw_material = App\Models\RawMaterial::find($product->id);   
@endphp --}}
<li>
    <strong>
        {{ $product->name }} ({{ $product->storages[0]->quantity }} pzs.)
    </strong>.
    Punto de reposición: {{ $product->min_quantity }} <br>
    Punto máximo sugerido: {{ $product->max_quantity }}
</li>
@endforeach

{{ $salutation }}

@component('mail::button', ['url' => $url])
Ver en ERP
@endcomponent
@endcomponent