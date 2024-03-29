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
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'No autorizado',
            'payroll_user' => $this->whenLoaded('payrollUser'),
            // 'payroll_user_id' => $this->payroll_user_id,
            'user' => $this->whenLoaded('user'), //quitar*
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A') ?? '--',
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A') ?? '--',
        ];
    }
}
