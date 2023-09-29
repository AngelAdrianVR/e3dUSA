<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'project_name' => $this->project_name,
            'group' => $this->group,
            'status' => $this->status,
            'description' => $this->description,
            'is_strict_project' => $this->is_strict_project,
            'is_internal_project' => $this->is_internal_project,
            'currency' => $this->currency,
            'sat_method' => $this->sat_method,
            'budget' => $this->budget,
            'type_access_project' => $this->type_access_project,
            'user' => $this->whenLoaded('user'),
            'company' => $this->whenLoaded('company'),
            'company_branch' => $this->company_branch,
            'sale_id' => $this->sale_id,
            'user' => $this->whenLoaded('user'),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'start_date' => $this->start_date?->isoFormat('DD MMM YYYY'),
            'limit_date' => $this->limit_date?->isoFormat('DD MMM YYYY'),
            'finished_at' => $this->finished_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
