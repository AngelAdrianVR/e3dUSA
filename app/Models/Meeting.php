<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'location',
        'url',
        'authorized_at',
        'description',
        'date',
        'start',
        'end',
        'participants',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'authorized_at' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
        'participants' => 'array',
    ];

    // relationships
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'id',
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
