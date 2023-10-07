<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->priority == 'Baja') {
            $priority = [
                'label' => 'Baja',
                'color' => 'text-[#87CEEB]'
            ];
        } else if ($this->priority == 'Media') {
            $priority = [
                'label' => 'Media',
                'color' => 'text-orange-500'
            ];
        } else if ($this->priority == 'Alta') {
            $priority = [
                'label' => 'Alta',
                'color' => 'text-red-600'
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact' => $this->contact,
            'amount' => $this->amount,
            'status' => $this->status,
            'description' => $this->description,
            'tags' => $this->tags,
            'probability' => $this->probability,
            'type_access_project' => $this->type_access_project,
            'priority' => $priority,
            'finished_at' => $this->finished_at?->isoFormat('DD MMMM YYYY'),
            'start_date' => $this->star_date?->isoFormat('DD MMMM YYYY'),
            'media' => $this->getMedia('files')->all(),
            'estimated_finish_date' => $this->estimated_finish_date?->isoFormat('DD MMMM YYYY'),
            'company' => $this->whenLoaded('company'),
            'user' => $this->whenLoaded('user'),
            'oportunityTasks' => $this->whenLoaded('oportunityTasks'),
            'created_at' => [
                'diffForHumans' => $this->created_at?->diffForHumans(),
                'isoFormat' => $this->created_at?->isoFormat('DD MMMM YYYY'),                
                ],
            'updated_at' => $this->created_at?->diffForHumans(),
        ];
    }
}