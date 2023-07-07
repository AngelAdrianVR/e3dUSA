<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'status',
        'notes',
        'authorized_user_name',
        'authorized_at',
        'expected_delivery_date',
        'emited_at',
        'recieved_at',
        'is_iva_included',
        'user_id',
        'supplier_id',
        'products',
        'bank_information',
        'contact_id'
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'expected_delivery_date' => 'date',
        'emited_at' => 'datetime',
        'recieved_at' => 'datetime',
        'products' => 'array',
        'bank_information' => 'array',
    ];

    //relationships
    
    public function contact(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
