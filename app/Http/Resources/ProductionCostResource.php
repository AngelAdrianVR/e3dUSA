<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'description'=> $this->description,
            'cost'=> ['raw' => $this->cost,
                      'format' => '$' . number_format($this->cost,2)
                        ],
            'created_at'=> $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at'=> $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
