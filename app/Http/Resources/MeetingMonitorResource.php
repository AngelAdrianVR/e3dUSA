<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingMonitorResource extends JsonResource
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
            'meeting_date' => $this->meeting_date?->isoFormat('DD MMMM YYYY'),
            'time' => $this->time,
            'meeting_via' => $this->meeting_via,
            'location' => $this->location,
            'phone' => $this->phone,
            'description' => $this->description,
            'company' => $this->whenLoaded('company'),
            'companyBranch' => $this->whenLoaded('companyBranch'),
            'oportunity' => OportunityResource::make($this->whenLoaded('oportunity')),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'seller' => $this->whenLoaded('seller'),
            'created_at' => $this->created_at?->isoFOrmat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFOrmat('DD MMMM YYYY'),
        ];
    }
}
