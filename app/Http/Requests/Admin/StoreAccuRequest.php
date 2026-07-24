<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:45',
            'name' => 'required|string|max:45',
            'berat_kering' => 'required|numeric|min:0',
            'percentages' => 'nullable|array',
            'percentages.*' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
