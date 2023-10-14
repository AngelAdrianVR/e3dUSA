<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyBranchResource extends JsonResource
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
        'address' => $this->address,
        'post_code' => $this->post_code,
        'sat_method' => $this->sat_method,
        'sat_type' => $this->sat_type,
        'sat_way' => $this->sat_way,
        'company' => $this->whenLoaded('company'),
        'quotes' => QuoteResource::collection($this->whenLoaded('quotes')),
        'sales' => $this->whenLoaded('sales'),
        'contacts' => $this->whenLoaded('contacts'),
        'important_notes' => $this->important_notes,
        'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
        'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
