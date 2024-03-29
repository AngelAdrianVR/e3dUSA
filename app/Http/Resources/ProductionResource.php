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
            'has_low_stock' => $this->has_low_stock,
            'supervision' => $this->supervision,
            'user' => $this->whenLoaded('user'),
            'progress' => $this->whenLoaded('progress'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
