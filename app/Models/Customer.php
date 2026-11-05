<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
        protected $fillable = [
        'name',
        'email',
        'phone',
        'gstin',
        'address',
    ];

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }
}
