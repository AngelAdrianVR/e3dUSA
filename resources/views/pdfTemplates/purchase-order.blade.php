<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OC-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 0.5cm
        }

        table {
            border-collapse: collapse;
            /* Colapsar los bordes de la tabla */
        }

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

        .footer {
            margin: 32px 32px 0 32px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            column-gap: 0.75rem;
            row-gap: 0.25rem
        }
    </style>
</head>

<body style="font-size: 11px; font-family: sans-serif">
    <header>
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <img width="30%" src="{{ public_path('images\logo.png') }}">
            <p style="background-color:#D9D9D9; font-weight: bold; padding: 1px 20px; text-align: center;">
                Orden de compra
            </p>
        </div>
        <div style="display: flex; flex-direction: column; margin-right: 32px; padding-left: 590px; width: 100%">
            <span style="width: 192px; margin-left: auto">
                <span style="margin-right: 24px; width: 50%">Folio:</span>
                <span style="width: 50%; margin-right: 8px">OC-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</span>
            </span> <br>
            <span style="width: 192px; margin-left: auto">
                <span style="margin-right: 24px; width: 50%">Fecha:</span>
                <span style="width: 50%">{{ $purchase->created_at->isoFormat('DD MMMM, YYYY') }}</span>
            </span>
        </div>
        <div style="margin-top: 5px; padding: 4px 5px; background-color: #D9D9D9">
            <span>EMBLEMS 3D USA</span><br>
            <span>Av. Aurelio Ortega 518, Seattle, 45150 Zapopan, Jal.</span><br>
            <span>EDU211206DC9</span><br>
            <span>33 38338209</span>
        </div>
        <section style="margin: 5px 16px 0 16px; padding: 4px 6px;">
            <span>Proveedor:</span>
            <span>{{ $purchase->supplier->name }}</span> <br>
            <span>Domicilio:</span>
            <span>{{ $purchase->supplier->address }}</span> <br>
            <span>Telefono:</span>
            <span>{{ $purchase->supplier->phone }}</span> <br>
            <span>Cuenta bancaria:</span>
            <span>
                {{ $purchase->supplier->banks[$purchase->bank_information]['accountNumber'] }}
                ({{ $purchase->supplier->banks[$purchase->bank_information]['bank_name'] }})
            </span> <br>
            <span>Observaciones: </span>
            <span>{{ $purchase->notes ?? '-' }}</span>
        </section>
    </header>
    <main style="margin-top: 5px">
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
        <section class="footer">
            <section style="width: 40%; margin-right: 8px; margin-left: auto; text-align: right">
                @php
                    $subtotal = $raw_materials
                        ->map(function ($item) use ($purchase) {
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
                <span
                    style="font-weight: bold; border-top-width: 2px; border-bottom-width: 2px; border-color: rgb(154 154 154 / var(--tw-border-opacity));">
                    {{ number_format($subtotal * 1.16, 2) }}</span> <br>
            </section>
        </section>
        <!-- imagenes -->
        <section>
            <div style="margin-top: 32px; margin-bottom: 12px">
                @foreach ($raw_materials as $item)
                    @php
                        $path = $item->media[0]['id'] . '\\' . $item->media[0]['file_name'];
                    @endphp
                    <div
                        style="display: inline-block; width: 24%; height: 100px; margin-right: 4px; margin-bottom: 4px; font-size: 10px; background-color: #D9D9D9; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.375rem; border-bottom-left-radius: 0.375rem">
                        <img width="100%" src="{{ public_path("storage\\$path") }}">
                        <p
                            style="color: dimgray; background-color: #D9D9D9; padding: 2px; margin: 0; text-transform: uppercase">
                            {{ $item->part_number }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </main>
</body>

</html>
