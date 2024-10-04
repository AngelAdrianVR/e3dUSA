<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'status', //0:pendiente 1:Autorizado 2:finalizado 3:recibido
        'notes',
        'currency',
        'authorized_user_name',
        'is_spanish_template',
        'authorized_at',
        'expected_delivery_date',
        'emited_at',
        'recieved_at',
        'is_iva_included',
        'show_prices',
        'user_id',
        'supplier_id',
        'products',
        'bank_information',
        'contact_id',
        'rating'
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'expected_delivery_date' => 'date',
        'emited_at' => 'datetime',
        'recieved_at' => 'datetime',
        'products' => 'array',
        'bank_information' => 'array',
        'rating' => 'array',
    ];

    //relationships
    public function contact(): MorphOne
    {
        return $this->morphOne(Contact::class, 'contactable');
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
