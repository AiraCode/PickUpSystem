<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:45',
            'address' => 'required|string|max:45',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ];
    }
}
