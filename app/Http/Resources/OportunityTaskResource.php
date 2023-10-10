<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OportunityTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $deadline = Carbon::parse($this->limit_date);

        $today = Carbon::today();
        $nextWeek = Carbon::today()->addWeek();

        $deadlineStatus = '';

        if ($deadline->isSameDay($today)) {
            $deadlineStatus = 'Terminar hoy';
        } elseif ($deadline->isBetween($today, $nextWeek)) {
            $deadlineStatus = 'Esta semana';
        } elseif ($deadline->gt($nextWeek)) {
            $deadlineStatus = 'Proximas';
        } elseif ($deadline->lt($today)) {
            $deadlineStatus = 'Atrasadas';
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'limit_date' => $this->limit_date?->isoFormat('DD MMMM YYYY'),
            'limit_date_raw' => $this->limit_date,
            'time' => Carbon::parse($this->time)->format('h:i A'),
            'time_raw' => $this->time,
            'finished_at' => $this->finished_at?->isoFormat('DD MMMM YYYY, h:mmA'),
            'description' => $this->description,
            'priority' => $this->priority,
            'reminder' => $this->reminder,
            'user' => $this->whenLoaded('user'),
            'oportunity' => $this->whenLoaded('oportunity'),
            'asigned' => $this->whenLoaded('asigned'),
            'comments' => $this->whenLoaded('comments'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
            'deadline_status' => $deadlineStatus,
        ];
    }
}
