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
}
