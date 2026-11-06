<?php

namespace App\Http\Requests;

use App\Enums\BudgetPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'period_type' => ['required', Rule::enum(BudgetPeriod::class)],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['nullable', 'required_if:period_type,monthly', 'integer', 'between:1,12'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }
}

