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

    public function getTotalSpentAttribute(): float
    {
        return (float) ($this->expenses_sum_amount ?? $this->expenses()->sum('amount'));
    }

    public function getRemainingAttribute(): float
    {
        return (float) ($this->amount - $this->total_spent);
    }

    public function getSpentPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return min(100, round(($this->total_spent / $this->amount) * 100, 1));
    }

    public function getPeriodLabelAttribute(): string
    {
        if ($this->period_type === BudgetPeriod::MONTHLY && $this->month) {
            $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
            return $monthName . ' ' . $this->year;
        }

        return 'Year ' . $this->year;
    }
}
