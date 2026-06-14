<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        return [
            'name'    => ['sometimes', 'string', 'max:255'],
            'email'   => ['sometimes', 'email', Rule::unique('suppliers', 'email')->ignore($supplier)],
            'phone'   => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ];
    }
}
