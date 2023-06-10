<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CatalogProduct extends Model
{
    use HasFactory;

    // relationships

    /**
     * Get the CatalogProduct's storage in warehouse.
     */
    public function storage(): MorphOne
    {
        return $this->morphOne(Storage::class, 'storageable');
    }
}
