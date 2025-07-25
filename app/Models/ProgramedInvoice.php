<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramedInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'programed_by',
        'reminder_date',
        'reminder_time',
        'amount',
        'number_of_invoice',
        'total_amount_sale',
        'invoice_quantity', //numero total de facturas relacionadas con la misma ov
        'status', // Pendiente, Creada
        'sale_id',
        'company_branch_id',
        'user_id',
    ];

    protected $casts = [
        'reminder_date' => 'date',
    ];

    // Relaciones 
    public function sale() :BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function companyBranch() :BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }
}
