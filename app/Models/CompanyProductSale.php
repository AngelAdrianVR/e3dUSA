<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProductSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'notes',
        'status',
        'assinged_jobs',
        'sale_id',
        'company_product_id'
    ];  

    protected $casts = [
        'assinged_jobs' => 'array',
    ];  
}
