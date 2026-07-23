<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:45',
            'address' => 'sometimes|required|string|max:45',
            'lat' => 'sometimes|required|numeric',
            'long' => 'sometimes|required|numeric',
        ];
    }
}
