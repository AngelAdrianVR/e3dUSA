<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'sent_at',
        'returned_at',
        'sale_order_at',
        'comments',
        'catalog_product_id',
        'company_branch_id',
        'user_id',
        'contact_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'returned_at' => 'datetime',
        'sale_order_at' => 'datetime',
    ];

    //relationships
    public function catalogProduct():BelongsTo 
    {
        return $this->belongsTo(CatalogProduct::class);
    }

    public function companyBranch():BelongsTo 
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function user():BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
