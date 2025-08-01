<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CompanyBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'password', // se utiliza para ingresar a la app de clientes
        'address',
        'state',
        'post_code',
        'meet_way',
        'sat_method',
        'sat_type',
        'sat_way',
        'company_id',
        'important_notes',
        'days_to_reactivate',
    ];

    protected $hidden = [
        'password'
    ];

    //relationships
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function programedInvoices(): HasMany
    {
        return $this->hasMany(ProgramedInvoice::class);
    }

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales():HasMany
    {
        return $this->hasMany(Sale::class);
    }
    
    public function samples():HasMany
    {
        return $this->hasMany(Sample::class);
    }

    public function quotes():HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function designAuthorizations():HasMany
    {
        return $this->hasMany(DesignAuthorization::class);
    }

    public function invoices():HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
