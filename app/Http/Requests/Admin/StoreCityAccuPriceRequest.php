<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityAccuPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accus_id' => 'required|integer|exists:accus,id',
            'price' => 'required|integer|min:0',
        ];
    }
}
