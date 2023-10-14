<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'user_id',
        'type',
    ];

    // relationships

    /**
     * Get all of the projects that are assigned this tag
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

     /**
     * Get all of the customers that are assigned this tag
     */
    public function customers()
    {
        return $this->morphedByMany(Customer::class, 'taggable');
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
