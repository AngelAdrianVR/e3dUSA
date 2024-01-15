<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallMonitorResource extends JsonResource
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
            'contact_phone' => $this->contact_phone,
            'notes' => $this->notes,
            'date' => $this->date?->isoFormat('DD MMMM YYYY'),
            'date_raw' => $this->date,
            'company_name' => $this->company_name,
            'oportunity' => OportunityResource::make($this->whenLoaded('oportunity')),
            'company' => $this->whenLoaded('company'),
            'seller' => $this->whenLoaded('seller'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'companyBranch' => $this->whenLoaded('companyBranch'),
            'contact' => $this->whenLoaded('contact'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
