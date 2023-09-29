<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Obtener los nombres de las sucursales en un arreglo
    $companyBranchNames = $this->whenLoaded('companyBranches', function () {
        return $this->companyBranches->pluck('name')->toArray();
    });

        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'phone' => $this->phone,
            'rfc' => $this->rfc,
            'post_code' => $this->post_code,
            'fiscal_address' => $this->fiscal_address,
            'company_branches' => $this->whenLoaded('companyBranches'),
            'company_branches_names' => implode(', ', $companyBranchNames),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
