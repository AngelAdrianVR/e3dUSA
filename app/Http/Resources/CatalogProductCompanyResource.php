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
            'old_date' => $this->old_date,
            'new_date' => $this->new_date,
            'old_price' => $this->old_price,
            'new_price' => $this->new_price,
            'old_currency' => $this->old_currency,
            'new_currency' => $this->new_currency,
            'catalog_product' => CatalogProductResource::make($this->whenLoaded('catalogProduct')),
            'company' => $this->whenLoaded('company'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
