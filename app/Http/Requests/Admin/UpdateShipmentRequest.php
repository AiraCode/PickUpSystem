<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'storages_id' => 'sometimes|required|integer|exists:storages,id',
            'status' => 'sometimes|required|string|max:45',
            'pickup_date' => 'sometimes|required|date',
            'received_date' => 'sometimes|required|date',
        ];
    }
}
