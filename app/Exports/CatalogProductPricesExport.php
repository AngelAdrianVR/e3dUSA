<?php

namespace App\Exports;

use App\Models\CatalogProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize; // autozise en columnas

class CatalogProductPricesExport implements FromCollection, WithHeadings, WithColumnWidths
{
    public function collection()
    {
        $products = CatalogProduct::with(['companies'])->get();

        $data = [];

        foreach ($products as $product) {
            foreach ($product->companies as $company) {
                $data[] = [
                    'Producto' => $product->name,
                    'Precio' => $company->pivot->new_price ?? '-',
                    'Moneda' => $company->pivot->new_currency ?? '-',
                    // 'Fecha de cambio de precio' => $company->pivot->new_date ?? '-',
                    'Clientes' => $company->business_name,
                ];
            }
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'Nombre de producto',
            'Precio registrado',
            'Moneda',
            // 'Fecha de cambio de precio',
            'Clientes',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45, // Nombre de producto
            'B' => 16, // Precio registrado
            'C' => 10, // Moneda
            // 'D' => 25, // Fecha
            'E' => 50, // Cliente
        ];
    }

    // public function styles(Worksheet $sheet)
    // {
    //     return [
    //         1 => [
    //             'font' => ['bold' => true, 'size' => 12],
    //         ],
    //     ];
    // }
}
