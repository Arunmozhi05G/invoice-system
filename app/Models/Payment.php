<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
        protected $fillable = [
        'amount',
        'paid_on',
        'method',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_on' => 'date',
            'method' => PaymentMethod::class,
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
