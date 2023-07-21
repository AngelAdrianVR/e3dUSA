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
        'authorized_user_name',
        'authorized_at',
        'recieved_at',
        'user_id',
        'company_branch_id',
        'contact_id'
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'recieved_at' => 'datetime',

    ];

    //relationships
    public function contact(): BelongsTo
    {
        return $this->BelongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    // public function catalogProductsCompany(): BelongsToMany
    // {
    //     return $this->belongsToMany(CatalogProductCompany::class, 'catalog_product_company_sale', 'sale_id', 'catalog_product_company_id')
    //         ->withPivot('id', 'quantity', 'notes', 'status', 'assigned_jobs')
    //         ->withTimestamps()
    //         ->using(CatalogProductCompanySale::class);
    // }

    public function catalogProductCompanySales(): HasMany
    {
        return $this->HasMany(CatalogProductCompanySale::class);
    }

    public function productions()
    {
        return $this->hasManyThrough(Production::class, CatalogProductCompanySale::class, 'sale_id', 'catalog_product_company_sale_id');
    }
}
