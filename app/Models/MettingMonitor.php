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
        'description',
        'company_id',
    ];

    //relationships
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
