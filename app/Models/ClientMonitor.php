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
        'monitor_id', // no es una llave foranea.
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

    public function emailMonitor() :HasOne 
    {
        return $this->hasOne(EmailMonitor::class);
    }

    public function paymentMonitor() :HasOne 
    {
        return $this->hasOne(PaymentMonitor::class);
    }

    public function mettingMonitor() :HasOne 
    {
        return $this->hasOne(MettingMonitor::class);
    }

    public function whatsappMonitor() :HasOne 
    {
        return $this->hasOne(WhatsappMonitor::class);
    }

    public function callMonitor() :HasOne 
    {
        return $this->hasOne(CallMonitor::class);
    }
}
