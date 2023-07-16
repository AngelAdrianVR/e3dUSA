<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KioskDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'token',
    ];
}
