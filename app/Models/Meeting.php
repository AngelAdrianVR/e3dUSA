<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [];

    // relationships
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'comments',
                'attendance_confirmation',
            ])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
