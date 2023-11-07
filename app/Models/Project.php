<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $fillable = [
        'project_name',
        'project_group_id',
        //'status', // SE CALCULA EN LA VISTA
        'shipping_address',
        'currency',
        'sat_method',
        'description',
        'is_strict_project',
        'is_internal_project',
        'budget',
        'type_access_project',
        'start_date',
        'limit_date',
        'finished_at',
        'user_id',
        'owner_id',
        'company_id',
        'company_branch_id',
        'sat_method',
        'sale_id',
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'limit_date' => 'datetime',
        'finished_at' => 'datetime',
    ];

    //relationships
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function projectGroup()
    {
        return $this->belongsTo(ProjectGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
        ->withPivot([
            'id',
            'permissions',
        ])->withTimestamps();
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

}
