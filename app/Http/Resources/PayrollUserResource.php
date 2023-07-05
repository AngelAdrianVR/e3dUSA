<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $no_attendance = ['Descanso', 'Falta'];
        return [
            'id' => $this->id,
            'date' => [
                'estandard' => $this->date->isoFormat('DD-MM-YYYY'),
                'formatted' => $this->date->isoFormat('ddd, DD MMM'),
            ],
            'check_in' => $this->check_in?->isoFormat('HH:mm'),
            'start_break' => $this->start_break?->isoFormat('HH:mm'),
            'end_break' => $this->end_break?->isoFormat('HH:mm'),
            'check_out' => $this->check_out?->isoFormat('HH:mm'),
            'late' => $this->late,
            'extras_enabled' => $this->extras_enabled,
            'extra_hours' => $this->extra_hours,
            'extra_minutes' => $this->extra_minutes,
            'additionals' => $this->additionals,
            'total_break_time' => $this->totalBreakTime(),
            'total_worked_time' => $this->totalWorkedTime(),
            'incident' => $this->justificationEvent,
            'user_id' => $this->user_id,
            // 'incident' => in_array($this->justification_event_id, $no_attendance) ? $this->justification_event_id : $this->justificationEvent?->name ,
            // 'user' => $this->user,
            // 'payroll' => $this->payroll,
        ];
    }
}
