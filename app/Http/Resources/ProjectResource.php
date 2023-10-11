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
            'projectGroup' => $this->projectGroup,
            'status' => $this->status,
            'description' => $this->description,
            'shipping_address' => $this->shipping_address,
            'is_strict_project' => $this->is_strict_project,
            'is_internal_project' => $this->is_internal_project,
            'currency' => $this->currency,
            'sat_method' => $this->sat_method,
            'budget' => $this->budget,
            'type_access_project' => $this->type_access_project,
            'company' => $this->whenLoaded('company'),
            'companyBranch' => $this->companyBranch,
            'sale' => $this->sale,
            'user' => $this->whenLoaded('user'),
            'tags' => $this->whenLoaded('tags'),
            'users' => $this->whenLoaded('users'),
            'owner' => $this->whenLoaded('owner'),
            'media' => $this->getMedia()->all(),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'start_date' => $this->start_date?->isoFormat('DD MMM YYYY'),
            'limit_date' => $this->limit_date?->isoFormat('DD MMM YYYY'),
            'finished_at' => $this->finished_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
