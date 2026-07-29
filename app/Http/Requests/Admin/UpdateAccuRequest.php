<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:45',
                Rule::unique('accus', 'name')->ignore($this->route('accu'))->whereNull('deleted_at'),
            ],
            'berat_kering' => 'sometimes|required|numeric|min:0',
            'percentages' => 'nullable|array',
            'percentages.*' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nama aki ini sudah terdaftar. Silakan gunakan nama yang berbeda.',
        ];
    }
}
