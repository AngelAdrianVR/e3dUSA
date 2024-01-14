<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'profile_photo_url' => $this->profile_photo_url,
            'is_active' => [
                'string' => $this->is_active ? 'Activo' : 'Inactivo',
                'bool' => boolval($this->is_active),
            ],
            'employee_properties' => $this->employee_properties,
            'experience' => $this->calculateExperience(),
            'pivot' => PayrollUserResource::make($this->pivot),
        ];
    }

    // protected function calculateExperience()
    // {
    //     if (isset($this->employee_properties['join_date'])) {
    //         $joinDate = new Carbon($this->employee_properties['join_date']);
    //         $currentDate = Carbon::now();
    //         $monthsDifference = $currentDate->diffInMonths($joinDate);

    //         if ($monthsDifference < 6) {
    //             return 'Novato';
    //         } elseif ($monthsDifference >= 6 && $monthsDifference <= 12) {
    //             return 'Intermedio';
    //         } else {
    //             return 'Experto';
    //         }
    //     } else {
    //         // Si no se encuentra la fecha de ingreso, puedes manejarlo de la manera que desees, como establecer un valor predeterminado.
    //         return 'Super admin';
    //     }
    // }
}
