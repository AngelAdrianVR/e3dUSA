<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'folio',
        'issue_date',
        'total_amount_sale',
        'invoice_amount',
        'currency',
        'payment_method',
        'payment_option',
        'invoice_quantity',
        'status',
        'complements',
        'extra_invoices',
        'notes',
        'created_by',
        'company_branch_id',
        'sale_id',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'complements' => 'array',
        'extra_invoices' => 'array',
    ];

    //relaciones
    public function companyBranch()
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
