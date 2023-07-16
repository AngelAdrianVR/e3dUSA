<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_product_company_sale_id',
        'user_tasks',
        'user_id'
    ];

    protected $casts = [
        'user_tasks' => 'array'
    ];

    //relationships
    public function catalogProductCompanySale(): BelongsTo
    {
        return $this-> belongsTo(CatalogProductCompanySale::class);
    }

    public function user(): BelongsTo
    {
        return $this-> belongsTo(User::class);
    }
}
