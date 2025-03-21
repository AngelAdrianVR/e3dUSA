<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Obtener los nombres de las sucursales en un arreglo
        $companyBranchNames = $this->companyBranches->map(fn ($cb) => $cb->name)->all();

        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'phone' => $this->phone,
            'rfc' => $this->rfc,
            'post_code' => $this->post_code,
            'fiscal_address' => $this->fiscal_address,
            'branches_number' => $this->branches_number,
            'suggested_products' => $this->suggested_products,
            'seller' => $this->seller,
            'user' => $this->whenLoaded('user'),
            'company_branches' => CompanyBranchResource::collection($this->whenLoaded('companyBranches')),
            'catalogProducts' => CatalogProductResource::collection($this->whenLoaded('catalogProducts')),
            'oportunities' => OportunityResource::collection($this->whenLoaded('oportunities')),
            'clientMonitors' => ClientMonitorResource::collection($this->whenLoaded('clientMonitors')),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'company_branches_names' => implode(', ', $companyBranchNames),
            'created_at' => $this->created_at?->isoFormat('YYYY MMM DD'),
            'updated_at' => $this->updated_at?->isoFormat('YYYY MMM DD'),
        ];
    }
}
