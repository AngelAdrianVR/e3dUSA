<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'Esperando Autorización';

        if($this->authorized_at){
            $status = 'Autorizado. Sin iniciar';
        }elseif($this->started_at){
            $status = 'En proceso';
        }elseif ($this->status == 1) {
           $status = 'Terminado';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'plans_image' => $this->plans_image,
            'logo_image' => $this->logo_image,
            'company_branch_name' => $this->company_branch_name,
            'contact_name' => $this->contact_name,
            'dimensions' => $this->dimensions,
            'status' => $status,
            'design_data' => $this->design_data,
            'specifications' => $this->specifications,
            'pantones' => $this->pantones ?? 'N/A',
            'design_type_id' => $this->design_type_id,
            'designer' => $this->whenLoaded('designer'),
            'user' => $this->whenLoaded('user'),
            'mesure_unit' => $this->mesure_unit,
            'authorized_user_name' => $this->authorized_user_name ?? '--',
            'authorized_at' => $this->authorized_at?->isoFormat('YYYY MMM DD') ?? 'No autorizado',
            'expected_end_at' => $this->expected_end_at?->isoFormat('YYYY MMM DD') ?? '--',
            'original_design_id' => $this->original_design_id,
            'is_complex' => $this->is_complex,
            'reuse_percentage' => $this->reuse_percentage,
            'design_modifications' => $this->design_modifications,
            'started_at' => $this->started_at?->isoFormat('YYYY MMM DD') ?? 'No iniciado',
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
