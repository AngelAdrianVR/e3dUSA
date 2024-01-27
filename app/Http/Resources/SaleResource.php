<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => 'OV-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'p_folio' => 'OP-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'shipping_company' => $this->shipping_company,
            'freight_cost' => $this->freight_cost,
            'status' => $this->getStatus(),
            'oce_name' => $this->oce_name,
            'order_via' => $this->order_via,
            'tracking_guide' => $this->tracking_guide,
            'promise_date' => $this->promise_date?->isoFormat('DD MMMM YYYY') ?? '--',
            'invoice' => $this->invoice,
            'is_high_priority' => $this->is_high_priority,
            // 'products' => $this->whenLoaded('products'),
            'notes' => $this->notes ?? '--',
            'authorized_user_name' => $this->authorized_user_name ?? 'No autorizado',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'No autorizado',
            'recieved_at' => $this->recieved_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'user' => $this->whenLoaded('user'),
            'company_branch' => $this->whenLoaded('companyBranch'),
            'productions' => $this->whenLoaded('productions'),
            'catalogProductCompanySales' => $this->whenLoaded('catalogProductCompanySales'),
            'contact' => $this->whenLoaded('contact'),
            'oportunity' => $this->whenLoaded('oportunity'),
            'media' => $this->getMedia('oce')->all(),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
