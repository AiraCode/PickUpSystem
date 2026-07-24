<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:45|unique:cities,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nama kota sudah terdaftar.',
        ];
    }
}
