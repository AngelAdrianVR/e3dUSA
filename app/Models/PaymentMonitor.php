<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'paid_at',
        'amount',
        'payment_method',
        'concept',
        'notes',
        'oportunity_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime'
    ];

    //relationships
    public function oportunity() :BelongsTo
    {
        return $this->belongsTo(Oportunity::class);
    }
}
