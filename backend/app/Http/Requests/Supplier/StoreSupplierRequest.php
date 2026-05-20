<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id;

        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('suppliers')->ignore($supplierId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'tax_id' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'contact_name' => ['nullable', 'string'],
            'payment_terms' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
