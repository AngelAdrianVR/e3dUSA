<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayResource extends JsonResource
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
            'name' => $this->name,
            'date' => [
                'string' => $this->date->toDateString(),
                'formatted' => $this->date->isoFormat('DD MMMM'),
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
