<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignAuthorizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = [
            'label' => 'Pendiente',
            'color' => 'text-amber-500',
            'icon' => '<i class="fa-regular fa-clock"></i>',
        ];

        if ($this->design_accepted === 1) {
            $status = [
                'label' => 'Autorizado',
                'color' => 'text-green-500',
                'icon' => '<i class="fa-solid fa-check"></i>',
            ];
        } else if ($this->design_accepted === 0) {
            $status = [
                'label' => 'Rechazado',
                'color' => 'text-red-500',
                'icon' => '<i class="fa-solid fa-xmark"></i>',
            ];
        }

        return [
            'id' => $this->id,
            'version' => $this->version,
            'name' => $this->name,
            'color' => $this->color,
            'material' => $this-> ¿material,
            'engrave_method' => $this->engrave_method,
            'logistic' => $this->logistic,
            'quantity' => $this->quantity,
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM YYYY'),
            'responded_at' => $this->responded_at?->isoFormat('DD MMM YYYY'),
            'design_accepted' => $this->design_accepted,
            'status' => $status,
            'seller' => $this->whenLoaded('seller'),
            'company_branch' => $this->whenLoaded('companyBranch'),
            'contact_id' => $this->contact_id,
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
