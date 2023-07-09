<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoreAdditionalTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'Pendiente';
        if($this->authorized_at)
        $status = 'Autorizado';


        return [
            'id' => $this->id,
            'status' => $status,
            'justification' => $this->justification,
            'time_requested' => $this->time_requested,
            'comments' => $this->comments ?? '--',
            'authorized_user_name' => $this->authorized_user_name ?? '--',
            'authorized_at' => $this->authorized_at ?? 'No autorizado',
            'payroll' => $this->whenLoaded('payroll'),
            'user' => $this->whenLoaded('user'),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD') ?? '--',
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD') ?? '--',
        ];
    }
}
