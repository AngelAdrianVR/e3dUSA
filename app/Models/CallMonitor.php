<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_phone',
        'notes',
        'date',
        'oportunity_id',
        'company_id',
        'company_name',
        'seller_id',
        'client_monitor_id',
        'company_branch_id',
        'contact_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
    
    //relationships
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function companyBranch() :BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function oportunity() :BelongsTo
    {
        return $this->belongsTo(Oportunity::class);
    }

    public function seller() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientMonitor() :BelongsTo
    {
        return $this->belongsTo(ClientMonitor::class);
    }

    public function contact() :BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
