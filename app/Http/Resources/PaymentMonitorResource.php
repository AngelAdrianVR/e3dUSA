<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMonitorResource extends JsonResource
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
            'paid_at' => $this->paid_at?->isoFormat('DD MMMM YYYY, H:mm A'),
            'paid_at_raw' => $this->paid_at,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'concept' => $this->concept,
            'notes' => $this->notes,
            'media' => $this->getMedia()->all(),
            'oportunity' => OportunityResource::make($this->whenLoaded('oportunity')),
            'seller' => $this->whenLoaded('seller'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
