<?php

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialResource extends JsonResource
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
            'brand' => $this->brand,
            'is_luxury' => Brand::where('name', $this->brand)->first()?->is_luxury,
            'material' => $this->material,
            'width' => $this->width,
            'large' => $this->large,
            'height' => $this->height,
            'diameter' => $this->diameter,
            'part_number' => $this->part_number,
            'description' => $this->description,
            'measure_unit' => $this->measure_unit,
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'cost' => $this->cost,
            'pivot' => $this->pivot,
            'features' => $this->features,
            'storages' => $this->whenLoaded('storages'),
            // 'media' => $this->whenLoaded('media'),
            'isInCatalogProduct' => $this->isInCatalogProduct(),
            'media' => $this->getMedia()->all(),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
