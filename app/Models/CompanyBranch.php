<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CompanyBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'post_code',
        'sat_method',
        'sat_type',
        'sat_way',
        'company_id',
        'important_notes',
    ];

    //relationships
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales():HasMany
    {
        return $this->hasMany(sales::class);
    }
}
