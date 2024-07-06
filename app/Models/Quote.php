<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quote extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'receiver',
        'department',
        'tooling_cost',
        'tooling_currency',
        'tooling_cost_stroked',
        'freight_cost',
        'first_production_days',
        'notes',
        'currency',
        'authorized_user_name',
        'authorized_at',
        'responded_at',
        'is_spanish_template',
        'company_branch_id',
        'prospect_id',
        'user_id',
        'sale_id'
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    //relationships
    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }
    
    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function catalogProducts(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class)
            ->withPivot([
                'quantity',
                'price',
                'show_image',
                'notes',
            ])->withTimestamps();
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class)
            ->withPivot([
                'quantity',
                'price',
                'show_image',
                'notes',
            ])->withTimestamps();
    }

    public function getProfit()
    {
        $products = $this->catalogProducts;

        $totalSale = 0;
        $totalCost = 0;

        // Calcular el total de la venta y el costo total
        foreach ($products as $prd) {
            $totalSale += $prd->pivot->quantity * $prd->pivot->price;
            // el producto de catalogo ya toma en cuenta el precio de materia prima y costos de produccion
            $totalCost += $prd->pivot->quantity * $prd->cost;
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
            'currency' => $this->currency
        ];
    }
}
