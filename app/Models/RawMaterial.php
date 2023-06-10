<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RawMaterial extends Model
{
    use HasFactory;

    // relationships

    /**
     * Get the RawMaterial's sorage in warehose.
     */
    public function storage(): MorphOne
    {
        return $this->morphOne(Storage::class, 'storageable');
    }
}
