<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

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
        'mesure_unit',
        'authorized_user_name',
        'authorized_at',
        'expected_end_at',
        'original_design_id',
        'is_complex',
        'reuse_percentage',
        'started_at',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'started_at' => 'datetime',
        'expected_end_at' => 'datetime'
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
