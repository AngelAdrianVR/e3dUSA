<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_time',
        'max_time',
    ];

    // relationships
    public function designs()
    {
        return $this->hasMany(Design::class);
    }
}
