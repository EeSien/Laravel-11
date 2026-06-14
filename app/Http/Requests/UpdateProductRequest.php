<?php

namespace App\Http\Requests;

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
            'category_id'    => ['sometimes', 'integer', 'exists:categories,id'],
            'name'           => ['sometimes', 'string', 'max:255'],
            'sku'            => ['sometimes', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'description'    => ['nullable', 'string'],
            'price'          => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'is_active'      => ['boolean'],
            'supplier_ids'   => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
        ];
    }
}
