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
        if ($this->will_back) {

            $status = [
            'label' => 'Enviado. Esperando regreso de muestra',
            'bg-color' => 'bg-amber-600',
            'text-color' => 'text-amber-600',
            'border-color' => 'border-amber-600', 
            'description' => 'Esperando a que la muestra sea devuelta y obtengas retroalimentación',        
            'progress' => 'w-1/4'        
            ];

            if ($this->returned_at) {

                $status = ['label' => 'Muestra devuelta',
                    'bg-color' => 'bg-blue-600',
                    'text-color' => 'text-blue-600',
                    'border-color' => 'border-blue-600', 
                    'description' => 'Muestra devuelta. Da seguimiento para concretar venta',
                    'progress' => 'w-1/2'        
                    ];

                if ($this->requires_modification) {

                    $status = ['label' => 'Muestra enviada de nuevo con modificación',
                    'bg-color' => 'bg-sky-500',
                    'text-color' => 'text-sky-600',
                    'border-color' => 'border-sky-600', 
                    'description' => 'Muestra enviada de nuevo con modificaciones. Espera retroalimentación para finalizar con el seguimiento',
                    'progress' => 'w-3/4'        
                    ];

                }
            }
        } else {

            $status = [
            'label' => 'Enviado. Esperando respuesta',
            'bg-color' => 'bg-amber-600',
            'text-color' => 'text-amber-600',
            'border-color' => 'border-amber-500', 
            'description' => 'Muestra enviada. Esperando respuesta',
            'progress' => 'w-1/2'        
            ];

            if ($this->requires_modification) {

                $status = ['label' => 'Muestra enviada de nuevo con modificación',
                    'bg-color' => 'bg-sky-500',
                    'text-color' => 'text-sky-600',
                    'border-color' => 'border-sky-600', 
                    'description' => 'Muestra enviada de nuevo con modificaciones. Espera retroalimentación para finalizar con el seguimiento',
                    'progress' => 'w-3/4'        
                    ];

            }
        }

        if ($this->sale_order_at) {

            $status = [
            'label' => 'Orden generada. Venta exitosa',
            'bg-color' => 'bg-green-500',
            'text-color' => 'text-green-500',
            'border-color' => 'border-green-500', 
            'description' => '¡Venta cerrada!',
            'progress' => 'w-full'        
            ];

        } elseif ($this->denied_at) {

            $status = [
            'label' => 'Venta no concretada',
            'bg-color' => 'bg-primary',
            'text-color' => 'text-primary',
            'border-color' => 'border-primary', 
            'description' => 'Venta no concretada',       
            ];

        }       

        return [
            'id' => $this->id,
            'folio' => 'MUE-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'name' => $this->name,
            'will_back' => $this->will_back,
            'requires_modification' => $this->requires_modification,
            'denied_at' => $this->denied_at?->isoFormat('DD MMM, YYYY'),
            'devolution_date' => $this->devolution_date?->isoFormat('DD MMM, YYYY'),
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
            'user' => $this->whenLoaded('user'),
            'contact' => Contact::find($this->contact_id),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
