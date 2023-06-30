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
            'start_date' => $this->start_date?->isoFormat('YYYY MMM DD'),
            'end_date' => $this->start_date?->addDays(7)->isoFormat('YYYY MMM DD'),
            'week' => $this->week,
            'is_active' => $this->is_active,
            'users' => PayrollUserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
