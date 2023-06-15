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
        return [
            'id' => $this->id,
            'folio' => 'COT-' . str_pad($this->id, 3, "0", STR_PAD_LEFT),
            'receiver' => $this->receiver,
            'department' => $this->department,
            'tooling_cost' => $this->tooling_cost,
            'freight_cost' => $this->freight_cost,
            'first_production_days' => $this->first_production_days,
            'notes' => $this->notes ?? '--',
            'currency' => $this->currency,
            'authorized_user_name' => $this->authorized_user_name,
            'authorized_at' => $this->authorized_at->isoFormat('DD MMM, YYYY h:i A'),
            'is_spanish_template' => boolval($this->is_spanish_template),
            'company_branch' => $this->companyBranch,
            'user' => $this->user,
            'sale' => $this->sale,
            'products' => $this->catalogProducts,
        ];
    }
}
