<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => 'sometimes|required|string|max:45',
            'name' => 'sometimes|required|string|max:45',
            'berat_kering' => 'sometimes|required|numeric|min:0',
            'percentages' => 'nullable|array',
            'percentages.*' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
