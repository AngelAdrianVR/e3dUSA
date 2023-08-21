<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
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
            'start_date' => $this->start_date?->isoFormat('DD MMM, YYYY'),
            'end_date' => $this->start_date?->addDays(7)->isoFormat('DD MMM, YYYY'),
            'week' => $this->week,
            'is_active' => $this->is_active,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
