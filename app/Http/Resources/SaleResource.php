<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasStarted = $this->productions?->whereNotNull('started_at')->count();
        $hasNotFinished = $this->productions?->whereNull('finished_at')->count();

        if ($this->authorized_at == null) {
            $status = [
                'label' => 'Esperando autorización',
                'text-color' => 'text-amber-500',
                'border-color' => 'border-amber-500',
            ];
        } elseif ($this->productions) {
            if (!$hasStarted) {
                $status = [
                    'label' => 'Producción sin iniciar',
                    'text-color' => 'text-gray-500',
                    'border-color' => 'border-gray-500',
                ];
            } elseif ($hasStarted && $hasNotFinished) {
                $status = [
                    'label' => 'Producción en proceso',
                    'text-color' => 'text-blue-500',
                    'border-color' => 'border-blue-500',
                ];
            } else {
                $status = [
                    'label' => 'Producción terminada',
                    'text-color' => 'text-green-500',
                    'border-color' => 'border-green-500',
                ];
            }
        } else {
            $status = [
                'label' => 'Autorizado sin orden de producción',
                'text-color' => 'text-amber-500',
                'border-color' => 'border-amber-500',
            ];
        }

        return [
            'id' => $this->id,
            'folio' => 'OV-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'p_folio' => 'OP-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'shipping_company' => $this->shipping_company,
            'freight_cost' => $this->freight_cost,
            'status' => $status,
            'oce_name' => $this->oce_name,
            'order_via' => $this->order_via,
            'tracking_guide' => $this->tracking_guide,
            'invoice' => $this->invoice,
            'products' => $this->products,
            'notes' => $this->notes ?? '--',
            'authorized_user_name' => $this->authorized_user_name ?? 'No autorizado',
            'authorized_at' => $this->authorized_at?->isoFormat('DD MMM, YYYY h:mm A') ?? 'No autorizado',
            'recieved_at' => $this->recieved_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'user' => $this->whenLoaded('user'),
            'company_branch' => $this->whenLoaded('companyBranch'),
            'productions' => $this->whenLoaded('productions'),
            'catalogProductCompanySales' => $this->whenLoaded('catalogProductCompanySales'),
            'contact' => $this->whenLoaded('contact'),
            'oportunity' => $this->whenLoaded('oportunity'),
            'media' => $this->getMedia('oce')->all(),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
