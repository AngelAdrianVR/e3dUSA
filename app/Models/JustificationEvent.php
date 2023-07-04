<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JustificationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'additionals',
    ];

    protected $casts = [
        'additionals' => 'array'
    ];

    // relationships
    public function payrollUsers() :HasMany
    {
        return $this->hasMany(PayrollUser::class);
    }
}
