<?php

namespace App\Http\Resources;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = ['label' => 'Enviado. Esperando respuesta',
                    'text-color' => 'text-amber-500',
                    'border-color' => 'border-amber-500',        
                    'progress' => '1/3'        
                    ];

        if($this->returned_at) {

            $status = ['label' => 'Muestra devuelta',
                    'text-color' => 'text-blue-500',
                    'border-color' => 'border-blue-500',        
                    'progress' => '2/3'        
                    ];

                    if($this->sale_order_at){
                        $status = ['label' => 'Orden generada. Venta exitosa',
                    'text-color' => 'text-green-600',
                    'border-color' => 'border-green-600',        
                    'progress' => '3/3'        
                    ];
                    }
        }           

        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'sent_at' => $this->sent_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'returned_at' => $this->returned_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'sale_order_at' => $this->sale_order_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'comments' => $this->comments,
            'products' => $this->products,
            'status' => $status,
            'media' => $this->getMedia()->all(),
            'catalog_product' => CatalogProductResource::make($this->whenLoaded('catalogProduct')),
            'company_branch' => $this->whenLoaded('companyBranch'),
            'contact' => Contact::find($this->contact_id),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
