<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->priority == 'Normal') {
            $priority = [
                'label' => 'Normal',
                'color_border' => 'border-green-600'
            ];
        } else if ($this->priority == 'Medio') {
            $priority = [
                'label' => 'Medio',
                'color_border' => 'border-orange-600'
            ];
        } else if ($this->priority == 'Urgente') {
            $priority = [
                'label' => 'Urgente',
                'color_border' => 'border-red-600'
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $priority,
            'status' => $this->status,
            'is_paused' => $this->is_paused,
            'project' => $this->whenLoaded('project'),
            'users' => $this->whenLoaded('users'),
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
