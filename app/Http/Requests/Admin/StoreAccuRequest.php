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
            'id' => 'required|integer|unique:accus,id',
            'brand' => 'required|string|max:45',
            'name' => 'required|string|max:45',
        ];
    }
}
