<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class
Sale extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        // 'shipping_company',
        // 'freight_cost',
        // 'tracking_guide',
        // 'promise_date',
        'status',
        'oce_name',
        'order_via',
        'invoice',
        'notes',
        'is_high_priority',
        'is_new_design',
        'authorized_user_name',
        'authorized_at',
        'recieved_at',
        'sent_at',
        'sent_by',
        'shipping_type',
        'user_id',
        'company_branch_id',
        'contact_id',
        'oportunity_id',
        'partialities',
        'is_sale_production',
        'shipping_option',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'recieved_at' => 'datetime',
        'sent_at' => 'datetime',
        'partialities' => 'array',
        'promise_date' => 'date',
    ];

    //relationships
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function oportunity(): BelongsTo
    {
        return $this->belongsTo(Oportunity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function catalogProductCompanySales(): HasMany
    {
        return $this->hasMany(CatalogProductCompanySale::class);
    }

    public function productions()
    {
        return $this->hasManyThrough(Production::class, CatalogProductCompanySale::class, 'sale_id', 'catalog_product_company_sale_id');
    }

    // methods
    public function getTotalSoldAmount()
    {
        $totalAmount = 0;

        if ($this->authorized_at) { // solo si la orden esta autorizada
            // Obtén todas las relaciones de CatalogProductCompanySale asociadas a esta venta
            $catalogProductCompanySales = CatalogProductCompanySale::where('sale_id', $this->id)->get();

            foreach ($catalogProductCompanySales as $catalogProductCompanySale) {
                // Accede al modelo CatalogProductCompany a través de la relación
                $catalogProductCompany = $catalogProductCompanySale->catalogProductCompany;

                if ($catalogProductCompany) {
                    // Calcula el monto total para este producto
                    $quantity = $catalogProductCompanySale->quantity;
                    $price = $catalogProductCompany->new_price;

                    $totalProductAmount = $quantity * $price;

                    // Agrega el monto total de este producto al total general
                    $totalAmount += $totalProductAmount;
                }
            }
        }

        return $totalAmount;
    }

    public function getStatus()
    {
        $hasStarted = $this->productions?->whereNotNull('started_at')->count();
        $hasNotFinished = $this->productions?->whereNull('finished_at')->count();
        $lowStock = $this->productions?->where('has_low_stock', true)->count();

        if ($this->authorized_at == null) {
            $status = [
                'label' => 'Esperando autorización',
                'text-color' => 'text-amber-500',
                'border-color' => 'border-amber-500',
            ];
        } elseif ($this->productions) {
            if ($lowStock) {
                $status = [
                    'label' => 'Falta de materia prima',
                    'text-color' => 'text-red-500',
                    'border-color' => 'border-red-500',
                ];
            } elseif (!$hasStarted) {
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

        return $status;
    }

    public function getProfit()
    {
        $cpcs = $this->catalogProductCompanySales;

        $totalSale = 0;
        $totalCost = 0;
        $currency = '';

        // Calcular el total de la venta y el costo total
        foreach ($cpcs as $cpc) {
            $totalSale += $cpc->quantity * $cpc->catalogProductCompany?->new_price;
            // el producto de catalogo ya toma en cuenta el precio de materia prima y costos de produccion
            $totalCost += $cpc->quantity * $cpc->catalogProductCompany?->catalogProduct?->cost;
            $currency = $cpc->catalogProductCompany?->new_currency;
        }

        // Calcular la ganancia en dinero
        $profit = $totalSale - $totalCost;

        // Calcular la ganancia en porcentaje
        if ($totalCost > 0) {
            $profitPercentage = round(($profit / $totalCost) * 100);
        } else {
            $profit = 0;
            $profitPercentage = 0;
        }

        return [
            'money' => $profit,
            'percentage' => $profitPercentage,
            'total_sale' => $totalSale,
            'total_cost' => $totalCost,
            'currency' => $currency
        ];
    }
}
