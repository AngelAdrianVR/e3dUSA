<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'location',
        'date',
        'time',
        'reason',
        'contact_id',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // relationships
    public function user() :BelongsTo { 
        return $this->belongsTo(User::class);
    }

    public function contact() :BelongsTo { 
        return $this->belongsTo(Contact::class);
    }
}
