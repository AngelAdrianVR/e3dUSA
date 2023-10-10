<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientMonitorResource extends JsonResource
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
            'folio' => 'S-' . strtoupper(substr($this->oportunity?->name, 0, 3)) . '-' . strtoupper(substr($this->type, 0, 1)) . str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'type' => $this->type,
            'date' => $this->date?->isoFormat('DD MMMM YYYY, h:mm A'),
            'concept' => $this->concept,
            'seller' => $this->whenLoaded('seller'),
            'oportunity' => $this->whenLoaded('oportunity'),
            'company' => $this->whenLoaded('company'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
