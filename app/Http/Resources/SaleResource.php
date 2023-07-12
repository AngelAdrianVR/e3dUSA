<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $status = 'Esperando autorización';
        if($this->authorized_at){
            $status = 'Autorizado';
        }

        return [
            'id' => $this->id,
            'folio' => 'OV-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'shipping_company' => $this->shipping_company,
            'freight_cost' => $this->freight_cost,
            'status' => $status,
            'oce_name' => $this->oce_name,
            'order_via' => $this->order_via,
            'tracking_guide' => $this->tracking_guide,
            'invoice' => $this->invoice,
            'notes' => $this->notes ?? '--',
            'authorized_user_name' => $this->authorized_user_name ?? '--',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'No autorizado',
            'recieved_at' => $this->recieved_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'user' => $this->whenLoaded('user'),
            'company_branch' => $this->whenLoaded('companyBranch'),
            'contact' => $this->whenLoaded('contact'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
