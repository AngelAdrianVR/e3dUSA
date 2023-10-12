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
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'concept' => $this->concept,
            'notes' => $this->notes,
            'oportunity' => $this->whenLoaded('oportunity'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
