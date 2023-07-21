<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Design extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'plans_image',
        'logo_image',
        'company_branch_name',
        'contact_name',
        'dimensions',
        'status',
        'design_data',
        'specifications',
        'pantones',
        'designer_id',
        'design_type_id',
        'user_id',
        'measure_unit',
        'authorized_user_name',
        'authorized_at',
        'expected_end_at',
        'original_design_id',
        'is_complex',
        'reuse_percentage',
        'started_at',
        'finished_at',
        'design_modifications',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'expected_end_at' => 'datetime',
        'design_modifications' => 'array'
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designer()
    {
        return $this->belongsTo(User::class);
    }

    public function designType()
    {
        return $this->belongsTo(DesignType::class);
    }

    public function originalDesign()
    {
        return $this->belongsTo(self::class);
    }

}
