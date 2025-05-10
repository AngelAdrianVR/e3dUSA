<?php

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => strtoupper($this->name),
            'brand' => $this->brand,
            'is_luxury' => Brand::where('name', $this->brand)->first()->is_luxury,
            'description' => $this->description ?? '--',
            'part_number' => $this->part_number,
            'measure_unit' => $this->measure_unit,
            'width' => $this->width,
            'large' => $this->large,
            'height' => $this->height,
            'diameter' => $this->diameter,
            'media' => $this->getMedia()->all(),
            // 'pivot' => CatalogProductCompanyResource::make($this->pivot),
            'pivot' => $this->pivot,
            'cost' => [
               'raw' => $this->cost,
               'number_format' => number_format($this->cost, 2) . ' ' . '$MXN'
            ],
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'features' => [
                'raw' => $this->features ?? '--',
                'string' => implode(',',$this->features ?? []) ?? '--',
            ],
            'rawMaterials' => $this->rawMaterials,
            'storages' => $this->whenLoaded('storages'),
            'companies' => $this->whenLoaded('companies'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
