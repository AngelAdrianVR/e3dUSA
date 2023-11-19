<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityResource extends JsonResource
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
            'status' => $this->status,
            'media' => $this->getMedia()->all(),
            'product_inspection' => $this->product_inspection,
            'supervisor' => $this->whenLoaded('supervisor'),
            'production' => SaleResource::make($this->whenLoaded('production')),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY, h:m A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY, h:m A'),
        ];
    }
}
