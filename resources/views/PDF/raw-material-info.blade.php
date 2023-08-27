<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF document-Part number</title>
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

        .avatar {
            border-radius: 100px;
            background-color: #d9d9d9;
            width: 30px;
            height: 30px;
        }
    </style>
</head>

<body>
    <table class="styled-table">
        <thead>
            <tr>
                <th class="text-start">Image</th>
                <th class="text-start">Part number</th>
            </tr>
        </thead>
        <tbody>
            

            @foreach ($data as $item)
            @php
            if (isset( $item->storageable->media[0]->original_url)) 
                $var = explode('/', $item->storageable->media[0]->original_url)
            
            @endphp
                <tr>
                    <td>
                        @isset($item->storageable->media[0])
                           <a href="{{ 'https://intranetemblems3d.dtw.com.mx/storage/' .  $var[4] . '/' . $var[5] }}" target="_blank"> image </a>
                        @else
                            <div class="avatar"></div>
                        @endisset    

                        </td>
                        <td>{{ $item['storageable']['part_number'] }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </body>

    </html>
