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
        if ($this->authorized_at)
            $status = 'Autorizada';


        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'location' => $this->location,
            'url' => $this->url ?? '--',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'status' => $status,
            'description' => $this->description,
            'date' => $this->date?->isoFormat('dddd, DD MMMM'),
            'start' => [
                'raw' => $this->start,
                'format' => $this->start->isoFormat('h:mm a')
            ],
            'end' => [
                'raw' => $this->end,
                'format' => $this->end->isoFormat('h:mm a')
            ],
            'user' => $this->whenLoaded('user'),
            'participants' => $this->whenLoaded('users'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
