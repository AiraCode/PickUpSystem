<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|unique:users,id',
            'name' => 'required|string|max:45|unique:users,name',
            'password' => 'required|string|min:8',
        ];
    }
}
