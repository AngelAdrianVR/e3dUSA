<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'phone',
        'rfc',
        'post_code',
        'fiscal_address',
        'user_id',
        'seller_id',
    ];

    //relationships
    public function companyBranches(): HasMany
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function catalogProducts(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class)
            ->withPivot([
                'id',
                'old_date',
                'new_date',
                'old_price',
                'new_price',
                'old_currency',
                'new_currency',
            ])->withTimestamps()
            ->using(CatalogProductCompany::class);
    }

    public function oportunities(): HasMany
    {
        return $this->hasMany(Oportunity::class);
    }

    public function clientMonitors(): HasMany
    {
        return $this->hasMany(ClientMonitor::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
