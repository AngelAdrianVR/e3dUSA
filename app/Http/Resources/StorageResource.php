<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorageResource extends JsonResource
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
            'storageable' => $this->storageable,
            'quantity' => $this->quantity,
            'location' => $this->location,
            'type' => $this->type,
            'storageable' => $this->whenLoaded('storageable'),
            'movements' => $this->whenLoaded('movements'),
            'created_at' => $this->created_at,
        ];
    }
}
