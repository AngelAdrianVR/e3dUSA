<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'state',
        'contact_name',
        'contact_email',
        'contact_charge',
        'contact_phone',
        'contact_phone_extension',
        'contact_whatsapp',
        'status',
        'branches_number',
        'abstract',
        'user_id',
        'seller_id',
    ];

    // relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
