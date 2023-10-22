<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'date',
        'concept',
        'seller_id',
        'oportunity_id',
        'company_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    //relationships

    public function seller() :BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function oportunity() :BelongsTo 
    {
        return $this->belongsTo(Oportunity::class);
    }

    public function company() :BelongsTo 
    {
        return $this->belongsTo(company::class);
    }

    public function paymentMonitor() :HasOne 
    {
        return $this->hasOne(PaymentMonitor::class);
    }

    public function mettingMonitor() :HasOne 
    {
        return $this->hasOne(MettingMonitor::class);
    }
}
