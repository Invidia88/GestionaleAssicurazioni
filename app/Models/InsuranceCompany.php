<?php

namespace App\Models;

use Database\Factories\InsuranceCompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCompany extends Model
{
    /** @use HasFactory<InsuranceCompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_phone',
        'contact_email',
        'notes',
    ];

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }
}
