<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'action' => $this->action,
            'table_name' => $this->table_name,
            'record_id' => $this->record_id,
            'user_id' => $this->user_id,
            'old_data' => $this->old_data,
            'new_data' => $this->new_data,
            'created_at' => $this->created_at?->isoformat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoformat('DD MMM, YYYY h:mm A'),
        ];
    }
}
