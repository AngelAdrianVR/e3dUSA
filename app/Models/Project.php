<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_name',
        'owner',
        'group',
        'status',
        'is_strict_project',
        'budget',
        'type_access_project',
        'start_date',
        'limit_date',
        'finished_at',
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
}
