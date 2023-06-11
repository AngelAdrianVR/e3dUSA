<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CompanyProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_date',
        'new_date',
        'old_price',
        'new_price',
        'old_currency',
        'new_currency',
        'catalog_product_id',
        'company_id'
    ];

    //relationships

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function catalogProduct():BelongsTo
    {
        return $this->belongsTo(catalogProduct::class);
    }

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class)->using(CompanyProductSale::class)
                ->withPivot([
                    'quantity',
                    'notes',
                    'status',
                    'assinged_jobs'
                ])->withTimestamps();
    }
}
