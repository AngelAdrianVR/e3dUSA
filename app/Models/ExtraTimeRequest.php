<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraTimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'hours',
        'is_accepted',
        'user_id',
        'operator_id',
    ];

    protected $casts = ['date'];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function operator()
    {
        return $this->belongsTo(User::class);
    }
}
