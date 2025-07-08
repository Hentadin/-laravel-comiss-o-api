<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor_total' => 'required|numeric|min:0.01',
            'tipo_venda' => ['required', 'string', Rule::in(['direta', 'afiliada'])],
        ];
    }
}