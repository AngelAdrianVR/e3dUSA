<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MettingMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_date',
        'time',
        'meeting_via',
        'location',
        'phone',
        'description',
        'contact_name',
        'company_id',
        'company_branch_id',
        'oportunity_id',
        'seller_id',
        'client_monitor_id',
        'contact_id',
    ];

    protected $casts = [
        'meeting_date' => 'date'
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
