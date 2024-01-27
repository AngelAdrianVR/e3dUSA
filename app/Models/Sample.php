<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sample extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        'authorized_user_name',
        'authorized_at',
        'user_id',
        'products',
        'will_back',
        'requires_modification',
        'denied_at',
        'devolution_date',
        'contact_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'returned_at' => 'datetime',
        'sale_order_at' => 'datetime',
        'authorized_at' => 'datetime',
        'denied_at' => 'datetime',
        'devolution_date' => 'date',
        'products' => 'array',
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
