<?php

namespace App\Models;

use App\Enums\BudgetPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $fillable = [
        'name',
        'category',
        'period_type',
        'year',
        'month',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'period_type' => BudgetPeriod::class,
            'year' => 'integer',
            'month' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
