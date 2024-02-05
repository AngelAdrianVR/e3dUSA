<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock materia prima {{ now()->isoFormat('DD/MM/YYYY') }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.7em;
            font-family: sans-serif;
            width: 100%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .styled-table thead tr {
            background-color: #d90537;
            color: #ffffff;
            text-align: left;
        }

        .footer {
            background-color: #d90537;
            text-align: right;
            font-weight: bold;
            color: white;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #d90537;
        }

        .styled-table tbody tr.active-row {
            font-weight: bold;
            color: #d90537;
        }

        .text-red {
            color: red;
        }

        .text-orange {
            color: orange;
        }
    </style>
</head>

<body>
    <table class="styled-table">
        <caption>
            Inventario materia prima {{ now()->isoFormat('DD/MM/YYYY') }}
        </caption>
        <thead>
            <tr>
                <th class="text-start">Número de parte</th>
                <th class="text-start">Producto</th>
                <th class="text-start">Cantidad actual</th>
                <th class="text-start">Cantidad a pedir</th>
                <th class="text-start">Costo por unidad</th>
                <th class="text-end">$ por pedir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_cost = 0;
            @endphp
            @foreach ($data as $item)
                @php
                    $does_need_supply = $item['quantity'] <= $item['storageable']['min_quantity'];
                    $supply_quantity = $does_need_supply ? ($item['storageable']['max_quantity'] - $item['quantity']) : 0;
                    $cost = $supply_quantity * $item['storageable']['cost'];
                    $total_cost += $cost;
                @endphp
                <tr>
                    <td>{{ $item['storageable']['part_number'] }}</td>
                    <td>{{ $item['storageable']['name'] }}</td>
                    @if ($does_need_supply)
                         <td class="text-red">{{ number_format($item['quantity'], 2) }}</td>
                    @else
                        <td>{{ number_format($item['quantity'], 2) }}</td>
                    @endif
                    <td> {{ $supply_quantity }} </td>
                    <td> ${{ $item['storageable']['cost'] }} </td>
                    <td class="text-end"> ${{ number_format($cost, 2) }} </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <td colspan="6" class="footer">TOTAL: ${{ number_format($total_cost, 2) }}</td>
        </tfoot>
    </table>
</body>

</html>