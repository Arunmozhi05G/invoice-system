<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'type' => [
                'required',
                Rule::enum(ProductType::class),
            ],

            'unit' => ['nullable', 'string', 'max:20'],

            'price' => ['required', 'numeric', 'min:0'],

            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
