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
            'name' => $this->name,
            'description' => $this->description ?? '--',
            'part_number' => $this->part_number,
            'measure_unit' => $this->measure_unit,
            'cost' => [
               'raw' => $this->cost,
               'number_format' => number_format($this->cost, 2) . ' ' . '$MXN'
            ],
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'features' => $this->features,
            'rawMaterials' => $this->rawMaterials,
        ];
    }
}
