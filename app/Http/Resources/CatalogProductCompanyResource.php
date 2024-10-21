<?php

namespace App\Http\Resources;

use App\Models\CatalogProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogProductCompanyResource extends JsonResource
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
            'oldest_updated_by' => $this->oldest_updated_by,
            'oldest_price' => $this->oldest_price,
            'oldest_date' => $this->oldest_date?->isoFormat('DD MMM, YYYY h:mm A'),
            'oldest_currency' => $this->oldest_currency,
            'old_updated_by' => $this->old_updated_by,
            'old_date' => $this->old_date?->isoFormat('DD MMM, YYYY h:mm A'),
            'old_price' => $this->old_price,
            'old_currency' => $this->old_currency,
            'new_updated_by' => $this->new_updated_by,
            'new_date' => $this->new_date?->isoFormat('DD MMM, YYYY h:mm A'),
            'new_price' => $this->new_price,
            'new_currency' => $this->new_currency,
            'catalog_product' => CatalogProductResource::make($this->whenLoaded('catalogProduct')),
            'user' => $this->user,
            'company' => $this->whenLoaded('company'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
