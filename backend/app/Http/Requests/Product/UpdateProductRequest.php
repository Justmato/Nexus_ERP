<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['sometimes', 'string', 'max:50', Rule::unique('products')->ignore($this->product)],
            'barcode' => ['nullable', 'string', 'max:50'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['sometimes', 'string', 'max:20'],
            'cost_price' => ['numeric', 'min:0'],
            'sale_price' => ['sometimes', 'numeric', 'min:0'],
            'min_stock' => ['numeric', 'min:0'],
            'track_stock' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
