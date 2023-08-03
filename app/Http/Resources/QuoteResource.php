<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total_without_taxes = $this->catalogProducts->sum(function($item) {
            return $item->pivot->quantity * $item->pivot->price;
        });

        return [
            'id' => $this->id,
            'folio' => 'COT-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'receiver' => $this->receiver,
            'department' => $this->department,
            'tooling_cost' => $this->tooling_cost,
            'tooling_cost_stroked' => $this->tooling_cost_stroked,
            'freight_cost' => $this->freight_cost,
            'first_production_days' => $this->first_production_days,
            'notes' => $this->notes ?? '--',
            'currency' => $this->currency,
            'authorized_user_name' => $this->authorized_user_name ?? 'No autorizado',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'is_spanish_template' => boolval($this->is_spanish_template),
            'companyBranch' => $this->companyBranch,
            'user' => $this->user,
            'sale' => $this->sale,
            'products' => CatalogProductResource::collection($this->whenLoaded('catalogProducts')),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'total' => [
               'raw' => $total_without_taxes,
               'number_format' => number_format($total_without_taxes, 2),
            ],
        ];
    }
}
