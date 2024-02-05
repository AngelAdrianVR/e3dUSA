<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OC-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        th {
            background-color: #D9D9D9;
            padding: 8px 12px;
            border: solid 1px #9A9A9A;
            text-align: left
        }

        td {
            padding: 8px 12px;
            border: solid 1px #9A9A9A;
            text-align: left
        }

        footer {
            margin: 32px 32px 0 32px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            column-gap: 0.75rem;
            row-gap: 0.25rem
        }
    </style>
</head>

<body style="font-size: 11px">
    <header>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-left: 32px">
            E3dLogo
            <p style="background-color:#D9D9D9; font-weight: bold; padding: 1px 20px; text-align: center;">
                Orden de compra
            </p>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 32px; width: 100%">
            <p style="width: 192px; margin-left: auto">
                <span style="margin-right: 24px; width: 50%">Folio:</span>
                <span style="width: 50%; margin-right: 8px">OC-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</span>
            </p>
            <p style="width: 192px; margin-left: auto">
                <span style="margin-right: 24px; width: 50%">Fecha:</span>
                <span style="width: 50%">{{ $purchase->created_at->isoFormat('DD MMMM, YYYY') }}</span>
            </p>
        </div>
        <section
            style="width: 100%; margin: 16px 32px 0 32px; padding: 4px 16px; background-color: #D9D9D9">
            <p>EMBLEMS 3D USA</p>
            <p>Av. Aurelio Ortega 518, Seattle, 45150 Zapopan, Jal.</p>
            <p>EDU211206DC9</p>
            <p>33 38338209</p>
        </section>
        <section
            style="margin: 16px 32px 0 32px; padding: 4px 16px;">
            <span>Proveedor:</span>
            <span>{{ $purchase->supplier->name }}</span> <br>
            <span>Domicilio:</span>
            <span>{{ $purchase->supplier->address }}</span> <br>
            <span>Telefono:</span>
            <span>{{ $purchase->supplier->phone }}</span> <br>
            <span>Observaciones: </span>
            <span>{{ $purchase->notes ?? '-' }}</span>
        </section>
    </header>
    <main style="margin: 32px 14px 0 32px">
        <table style="width: 100%">
            <thead>
                <tr>
                    <th>Número de parte</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Valor unit.</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($raw_materials as $item)
                    <tr>
                        <td>{{ $item->part_number }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description }}</td>
                        @php
                            $products = collect($purchase->products);
                            $current_product = $products
                                ->filter(function ($element) use ($item) {
                                    return $element['id'] == $item->id;
                                })
                                ->first();
                        @endphp
                        <td><span>{{ $current_product['quantity'] }}</span></td>
                        <td>{{ $item->measure_unit }}</td>
                        <td>${{ number_format($item->cost, 2) }}</td>
                        <td>${{ number_format($item->cost * $current_product['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- imagenes -->
        {{-- <section>
            <div class="w-11/12 mx-auto my-3 grid grid-cols-5 gap-4 ">
                @foreach ($raw_materials as $item)
                    <div class="bg-gray-200 rounded-t-xl rounded-b-md border" style="font-size: 8px;">
                        <img class="rounded-t-xl max-h-52 mx-auto" src="{{ $item->media[0]->original_url }}">
                        <p class="py-px px-1 uppercase text-gray-600">{{ $item->name }}</p>
                    </div>
                @endforeach
            </div>
        </section> --}}
    </main>
    <footer>
        <section style="width: 40%; margin-right: 32px; margin-left: auto; text-align: right">
            @php
                $subtotal = $raw_materials->map(function ($item) use ($purchase) {
                        $quantity = optional(collect($purchase->products)->firstWhere('id', $item['id']))['quantity'] ?? 0;
                        return $item['cost'] * $quantity;
                    })
                    ->sum();
            @endphp
            <span>Subtotal</span>
            <span>{{ number_format($subtotal, 2) }}</span> <br>
            <span>IVA</span>
            <span>{{ number_format($subtotal * 0.16, 2) }}</span> <br>
            <span>Total</span>
            <span style="font-weight: bold; border-top-width: 2px; border-bottom-width: 2px; border-color: rgb(154 154 154 / var(--tw-border-opacity));">
                {{ number_format($subtotal * 1.16, 2) }}</span> <br>
        </section>
    </footer>
</body>

</html>
