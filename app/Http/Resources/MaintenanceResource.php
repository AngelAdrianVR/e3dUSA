<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
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
            'problems' => $this->problems,
            'actions' => $this->actions,
            'cost' => number_format($this->cost),
            'maintenance_type_id' => $this->maintenance_type_id,
            'responsible' => $this->responsible,
            'media' => $this->getMedia()->all(),
            'machine_id' => $this->machine_id,
            'validated_by' => $this->validated_by,
            'start_date' => $this->start_date?->isoFormat('DD MMM, YYYY'),
            'validated_at' => $this->validated_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
