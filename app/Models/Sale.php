<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    use HasFactory;

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
    public function contact(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function catalogProductsCompany(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProductCompany::class)
            ->withPivot('quantity', 'notes', 'status', 'assigned_jobs')
            ->withTimestamps()
            ->using(CatalogProductCompanySale::class);
    }
}
