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
        $status = ['label'=>'Esperando Autorización',
                    'text-color' =>'text-amber-500',
                    'border-color' => 'border-amber-500'        
                ];

        if($this->authorized_at){
            $status = ['label'=>'Autorizado. Sin iniciar',
            'text-color' =>'text-amber-700',
            'border-color' => 'border-amber-700'];
            if($this->started_at){
                $status = ['label'=>'En proceso',
                'text-color' =>'text-[#0355B5]',
                'border-color' => 'border-[#0355B5]'];
            if ($this->finished_at) {
                    $status = ['label'=>'Terminado',
                    'text-color' =>'text-green-600',
                    'border-color' => 'border-green-600'];
                 }
            }
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
            'design_type' => $this->whenLoaded('designType'),
            'designer' => $this->whenLoaded('designer'),
            'user' => $this->whenLoaded('user'),
            'media' => $this->getMedia('results')->all(),
            'media_plano' => $this->getMedia('plano')->all(),
            'media_logo' => $this->getMedia('logo')->all(),
            'measure_unit' => $this->measure_unit,
            'authorized_user_name' => $this->authorized_user_name ?? '--',
            'authorized_at' => $this->authorized_at?->isoFormat('YYYY MMM DD') ?? 'No autorizado',
            'expected_end_at' => $this->expected_end_at?->isoFormat('YYYY MMM DD') ?? '--',
            'original_design_id' => $this->original_design_id,
            'is_complex' => $this->is_complex,
            'reuse_percentage' => $this->reuse_percentage,
            'modifications' => $this->whenLoaded('modifications'),
            'design_modifications' => $this->design_modifications,
            'started_at' => $this->started_at?->isoFormat('YYYY MMM DD') ?? 'No iniciado',
            'finished_at' => $this->finished_at?->isoFormat('YYYY MMM DD'),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
