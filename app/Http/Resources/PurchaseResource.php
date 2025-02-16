<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->status == 0){
            $status = 'Pendiente';
        }elseif($this->status == 1){
            $status = 'Autorizado';
        }elseif($this->status == 2){
            $status = 'Emitido';
        }else{
            $status = 'Recibido';
        }

        return [
            'folio' => 'OC-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'id' => $this->id,
            'status' =>  $status,
            'currency' =>  $this->currency,
            'notes' => $this->notes ?? '--',
            'authorized_user_name' => $this->authorized_user_name ?? 'No autorizado',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'No autorizado',
            'expected_delivery_date' => $this->expected_delivery_date?->isoFormat('YYYY MMM DD') ?? 'Pedido no realizado',
            'emited_at' => $this->emited_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'Pedido no realizado',
            'recieved_at' => $this->recieved_at?->isoFormat('YYYY MMM DD') ?? 'No recibido',
            'is_iva_included' => $this->is_iva_included,
            'bank_information' => $this->bank_information,
            'additional_stock' => $this->additional_stock,
            'plane_stock' => $this->plane_stock,
            'ship_stock' => $this->ship_stock,
            'products' => $this->products,
            'carrier' => $this->carrier,
            'invoice_folio' => $this->invoice_folio,
            'user' => $this->whenLoaded('user'),
            'supplier' => $this->whenLoaded('supplier'),
            'contact' => $this->whenLoaded('contact'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
