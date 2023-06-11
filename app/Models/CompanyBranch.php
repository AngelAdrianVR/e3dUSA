<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'company_id'
    ];

    //relationships

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales():HasMany
    {
        return $this->hasMany(sales::class);
    }
}
