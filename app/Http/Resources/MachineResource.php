<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineResource extends JsonResource
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
            'serial_number' => $this->serial_number,
            'weight' => $this->weight ?? '--',
            'width' => $this->width ?? '--',
            'large' => $this->large ?? '--',
            'height' => $this->height ?? '--',
            'cost' => $this->cost ?? '--',
            'cost_number_format' => number_format($this->cost) ?? '--',
            'supplier' => $this->supplier ?? '--',
            'aquisition_date' => $this->aquisition_date?->isoFormat('YYYY MMM DD') ?? '--',
            'days_next_maintenance' => "{$this->days_next_maintenance} dias",
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
            'maintenances' => MaintenanceResource::collection($this->whenLoaded('maintenances')),
            'spare_parts' => SparePartResource::collection($this->whenLoaded('spareParts')),
            'media' => $this->getMedia('images')->all(),
            'files' => $this->getMedia('files')->all(),
        ];
    }
}
