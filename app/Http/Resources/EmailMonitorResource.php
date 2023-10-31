<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailMonitorResource extends JsonResource
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
            'subject' => $this->subject,
            'content' => $this->content,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'seller' => $this->whenLoaded('seller'),
            'oportunity' => $this->whenLoaded('oportunity'),
            'company' => $this->whenLoaded('company'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'created_at' => $this->created_at?->isoFOrmat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFOrmat('DD MMMM YYYY'),
        ];
    }
}
