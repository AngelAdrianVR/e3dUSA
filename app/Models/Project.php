<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_name',
        'group',
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
        'company_id',
        'company_branch_id',
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


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
