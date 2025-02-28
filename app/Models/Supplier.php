<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nickname',
        'address',
        'post_code',
        'phone',
        'banks',
        'raw_materials_id',
        'contact_id'
    ];

    protected $casts = [
        'banks' => 'array',
        'raw_materials_id' => 'array',
    ];  

    //relationships
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id', 'id');
    }
}
