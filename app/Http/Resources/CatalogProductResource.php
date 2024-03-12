<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => strtoupper($this->name),
            'description' => $this->description ?? '--',
            'part_number' => $this->part_number,
            'measure_unit' => $this->measure_unit,
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
