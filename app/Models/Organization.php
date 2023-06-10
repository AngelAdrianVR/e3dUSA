<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'rfc',
        'fiscal_address',
        'post_code',
        'phone_one',
        'phone_two',
        'logo',
        'shield',
        'website',
    ];

    
}
