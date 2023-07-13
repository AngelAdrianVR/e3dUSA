<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => [
                'string' => $this->is_active ? 'Activo' : 'Inactivo',
                'bool' => boolval($this->is_active),
            ],
            'employee_properties' => $this->employee_properties,
            'pivot' => PayrollUserResource::make($this->pivot),
        ];
    }
}