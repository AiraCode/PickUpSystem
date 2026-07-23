<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|string|max:45',
            'price_received' => 'sometimes|required|integer',
            'price_owed' => 'nullable|integer',
        ];
    }
}
