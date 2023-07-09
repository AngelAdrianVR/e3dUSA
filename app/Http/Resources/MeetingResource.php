<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'Pendiente';
        if($this->authorized_at)
        $status = 'Autorizada';


        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'location' => $this->location ?? '--',
            'url' => $this->url ?? '--',
            'authorized_at' => $this->authorized_at?->isoFormat('YYYY MMM DD') ?? '--',
            'status' => $status,
            'description' => $this->description,
            'date' => $this->date?->isoFormat('YYYY MMM DD') ?? '--',
            'start' => $this->start,
            'end' => $this->end,
            'user_id' => $this->whenLoaded('user'),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
