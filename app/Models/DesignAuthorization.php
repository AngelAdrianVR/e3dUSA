<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DesignAuthorization extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'version',
        'name',
        'color',
        'material',
        'engrave_method',
        'logistic',
        'quantity',
        'authorized_at',
        'responded_at',
        'design_accepted',
        'rejected_razon',
        'seller_id',
        'company_branch_id',
        'contact_id',
    ];

    protected $casts = [
        'authorized_at' => 'date',
        'responded_at' => 'date',
    ];

    //relationships
    public function seller() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyBranch() :BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }
}
