<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
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
            'catalog_product_company_sale' => $this->whenLoaded('catalogProductCompanySale'),
            'user_tasks' => $this->user_tasks,
            'user' => $this->whenLoaded('user'),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
