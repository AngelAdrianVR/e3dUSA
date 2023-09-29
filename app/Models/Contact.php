<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contactable_id',
        'contactable_type',
        'name',
        'email',
        'phone',
        'birthdate_day',
        'birthdate_month',
        'charge',
    ];

    //relationships

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }


}
