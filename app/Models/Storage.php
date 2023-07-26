<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = [
        'storageable',
        'quantity',
        'location',
        'type',
    ];

    // relationships

    /**
     * Get the parent storageable model (raw_material or catalog_product).
     */
    public function storageable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function lowStock()
    {
        return collect([]);
        return self::where('type', 'materia-prima')->whereHas('storageable', function ($query) {
            $query->whereColumn('storages.quantity', '<=', 'raw_materials.min_quantity');
        })->with('storageable')->get();
    }

    public function movements()
    {
        return $this->hasMany(StockMovementHistory::class);
    }
}
