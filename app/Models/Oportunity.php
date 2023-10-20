<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Oportunity extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'contact',
        'amount',
        'status',
        'priority',
        'description',
        'lost_oportunity_razon',
        'tags',
        'probability',
        'finished_at',
        'start_date',
        'estimated_finish_date',
        'type_access_project',
        'company_id',
        'user_id',
        'seller_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'finished_at' => 'datetime',
        'estimated_finish_date' => 'date',
        'tags' => 'array',
    ];

    //relationships
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function oportunityTasks() :HasMany
    {
        return $this->hasMany(OportunityTask::class);
    }

    public function clientMonitores() :HasMany
    {
        return $this->hasMany(ClientMonitor::class);
    }

    public function paymentMonitors() :HasMany
    {
        return $this->hasMany(PaymentMonitor::class);
    }

    public function mettingMonitors() :HasMany
    {
        return $this->hasMany(MettingMonitor::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function survey() :HasOne
    {
        return $this->hasOne(Survey::class);
    }
}
