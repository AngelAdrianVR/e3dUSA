<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'supplier',
        'cost',
        'description',
        'location',
        'machine_id',
    ];

    // relationships
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
