<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorageResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'location' => $this->location,
            'storageable' => $this->whenLoaded('storageable'),
            'created_at' => $this->created_at,
        ];
    }
}
