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
        return [
            'id' => $this->id,
            'date' => $this->date,
            'check_in' => $this->check_in,
            'start_break' => $this->start_break,
            'end_break' => $this->end_break,
            'check_out' => $this->check_out,
            'late' => $this->late,
            'extras_enabled' => $this->extras_enabled,
            'extra_hours' => $this->extra_hours,
            'extra_minutes' => $this->extra_minutes,
            'additionals' => $this->additionals,
        ];
    }
}
