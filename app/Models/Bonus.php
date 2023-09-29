<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'full_time',
        'half_time',
        'is_active',
    ];

    
}
