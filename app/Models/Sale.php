<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sale extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'shipping_company',
        'freight_cost',
        'status',
        'oce_name',
        'order_via',
        'tracking_guide',
        'invoice',
        'notes',
        'is_high_priority',
        'authorized_user_name',
        'authorized_at',
        'recieved_at',
        'user_id',
        'company_branch_id',
        'contact_id',
        'oportunity_id'
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'recieved_at' => 'datetime',

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
}
